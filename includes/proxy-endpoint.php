<?php
/**
 * HiGallery - veilige REST proxy voor HiDrive files
 *
 * Doel:
 * - Alleen bestanden binnen de ingestelde root folder serven (higallery_root_folder)
 * - Geen WP_REST_Response::set_body() gebruiken (bestaat niet)
 * - Binary output rechtstreeks streamen (voorkomt JSON-encoding / memory issues)
 * - (Optioneel) Range header doorgeven voor partial content (handig voor grote bestanden)
 */

add_action('rest_api_init', function () {
    register_rest_route('higallery/v1', '/file', [
        'methods'  => 'GET',
        'callback' => 'higallery_proxy_file',
        'permission_callback' => '__return_true',
        'args' => [
            'path' => [
                'required' => true,
                'type' => 'string',
                'validate_callback' => function ($param) {
                    return is_string($param) && $param !== '';
                },
            ],
        ],
    ]);

    register_rest_route('higallery/v1', '/thumb', [
        'methods'  => 'GET',
        'callback' => 'higallery_proxy_thumb',
        'permission_callback' => '__return_true',
        'args' => [
            'path' => [
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => function ($param) {
                    return is_string($param) && $param !== '';
                },
            ],
            'width' => [
                'required' => false,
                'sanitize_callback' => 'absint',
                'validate_callback' => function ($param) {
                    $w = (int) $param;
                    return $w === 0 || ($w >= 50 && $w <= 2000);
                },
            ],
        ],
    ]);

});

/**
 * Normaliseer en valideer paden zodat we geen '..', null bytes, CR/LF, etc accepteren.
 */
function higallery_normalize_and_validate_path($raw_path) {
    $raw_path = (string) $raw_path;

    // Decode 1x (WordPress kan al decoded zijn; we houden dit defensief)
    $path = rawurldecode($raw_path);

    // Basis checks
    if ($path === '' || $path[0] !== '/') {
        return new WP_Error('invalid_path', 'Ongeldig pad (moet met / beginnen)', ['status' => 400]);
    }

    // Blokkeer null bytes en header injection
    if (strpos($path, "\0") !== false || preg_match('/[\r\n]/', $path)) {
        return new WP_Error('invalid_path', 'Ongeldig pad (onveilige tekens)', ['status' => 400]);
    }

    // Blokkeer directory traversal
    if (strpos($path, '..') !== false) {
        return new WP_Error('invalid_path', 'Ongeldig pad (.. niet toegestaan)', ['status' => 400]);
    }

    // Root folder beperking (config uit settings)
    $root = (string) get_option('higallery_root_folder', '/');
    if ($root === '') { $root = '/'; }
    if ($root[0] !== '/') { $root = '/' . $root; }
    // trailing slash normaliseren behalve wanneer root = "/"
    if ($root !== '/') { $root = rtrim($root, '/'); }

    // Pad moet binnen root liggen
    if ($root !== '/' && strpos($path, $root . '/') !== 0 && $path !== $root) {
        return new WP_Error('forbidden_path', 'Pad valt buiten de ingestelde HiGallery root folder', ['status' => 403]);
    }

    return $path;
}

function higallery_proxy_file($request) {
    $raw_path = $request->get_param('path');
    $path = higallery_normalize_and_validate_path($raw_path);
    if (is_wp_error($path)) {
        return $path;
    }

    $token = higallery_get_valid_access_token();
    if (!$token) {
        return new WP_Error('no_token', 'Geen geldig access token', ['status' => 403]);
    }

    // Belangrijk: path moet URL-encoded zijn, anders breken spaties e.d.
    $url = 'https://api.hidrive.strato.com/2.1/file?path=' . rawurlencode($path);

    $headers = [
        'Authorization' => 'Bearer ' . $token,
    ];

    // Range support: browser stuurt soms Range bij media/grote files
    $range = $request->get_header('range');
    if (!empty($range)) {
        $headers['Range'] = $range;
    }


    $response = wp_remote_get($url, [
        'timeout' => 30,
        'headers' => $headers,
    ]);

    if (is_wp_error($response)) {
        return new WP_Error('api_error', 'Fout bij ophalen bestand', ['status' => 502]);
    }

    $code = (int) wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);

    if ($code !== 200 && $code !== 206) {
        return new WP_Error('hidrive_error', 'HiDrive gaf een foutmelding terug', [
            'status' => $code ?: 502,
        ]);
    }

    $content_type   = wp_remote_retrieve_header($response, 'content-type') ?: 'application/octet-stream';
    $content_length = wp_remote_retrieve_header($response, 'content-length');
    $content_range  = wp_remote_retrieve_header($response, 'content-range');

    // --- SAFE RAW OUTPUT ---
    // We streamen de binary direct zodat WordPress REST het NIET als JSON encodeert.
    // Dit is ook robuuster bij verschillen in WP versies / plugins / output filters.
    if (!headers_sent()) {
        status_header($code);

        header('Content-Type: ' . $content_type);
        if (!empty($content_length)) {
            header('Content-Length: ' . $content_length);
        } else {
            // Fallback
            header('Content-Length: ' . strlen($body));
        }

        if (!empty($content_range)) {
            header('Content-Range: ' . $content_range);
        }

        header('Cache-Control: public, max-age=3600');
        header('Accept-Ranges: bytes');
    }

    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- binary image bytes
    echo $body;
    exit;
}


/**
 * Proxy voor thumbnails. Gebruikt HiDrive thumbnail endpoint als beschikbaar.
 * Fallback: serve het originele bestand indien thumbnail endpoint faalt.
 */
function higallery_proxy_thumb($request) {
    $raw_path = $request->get_param('path');
    $path = higallery_normalize_and_validate_path($raw_path);
    if (is_wp_error($path)) {
        return $path;
    }

    $width = (int) $request->get_param('width');
    if ($width <= 0) {
        $width = (int) get_option('higallery_thumbnail_size', 300);
        if ($width <= 0) { $width = 300; }
    }


    $height = (int) $request->get_param('height');
    if ($height <= 0) {
        // default: zelfde als width (thumbnail bounding box)
        $height = $width;
    }
    $token = higallery_get_valid_access_token();
    if (!$token) {
        return new WP_Error('no_token', 'Geen geldig access token', ['status' => 403]);
    }

    $headers = [
        'Authorization' => 'Bearer ' . $token,
        'User-Agent'    => 'HiGallery-WordPress/' . (defined('HIGALLERY_VERSION') ? HIGALLERY_VERSION : '1.0.2'),
    ];

    // Probeer thumbnail endpoint
    $thumb_url = 'https://api.hidrive.strato.com/2.1/file/thumbnail?path=' . rawurlencode($path) . '&width=' . $width . '&height=' . $height;

    $tmp = wp_tempnam('higallery-thumb-');
    $response = wp_remote_get($thumb_url, [
        'timeout'  => 30,
        'headers'  => $headers,
        'stream'   => true,
        'filename' => $tmp,
    ]);

    if (is_wp_error($response)) {
        if (is_string($tmp) && file_exists($tmp)) { wp_delete_file($tmp); }
        // fallback naar originele file proxy
        return higallery_proxy_file($request);
    }

    $code = (int) wp_remote_retrieve_response_code($response);
    if ($code !== 200) {
        if (is_string($tmp) && file_exists($tmp)) { wp_delete_file($tmp); }
        // fallback naar originele file proxy
        return higallery_proxy_file($request);
    }

    $content_type   = wp_remote_retrieve_header($response, 'content-type') ?: 'image/jpeg';
    $content_length = wp_remote_retrieve_header($response, 'content-length');

    // Binary output
    nocache_headers(); // thumbnails zijn snel te regenereren; browser mag wel cachen
    if (!headers_sent()) {
        header('Content-Type: ' . $content_type);
        if (!empty($content_length)) {
            header('Content-Length: ' . $content_length);
        } elseif (is_string($tmp) && file_exists($tmp)) {
            header('Content-Length: ' . filesize($tmp));
        }
        header('Cache-Control: public, max-age=3600');
    }

    if (is_string($tmp) && file_exists($tmp)) {
        // Gebruik WP_Filesystem i.p.v. readfile() (Plugin Directory best practice).
        if (!function_exists('WP_Filesystem')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        WP_Filesystem();
        global $wp_filesystem;

        $contents = $wp_filesystem ? $wp_filesystem->get_contents($tmp) : file_get_contents($tmp);
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- binary image bytes
        echo $contents;

        wp_delete_file($tmp);
        exit;
    }

    // Als er om wat voor reden dan ook geen tmp bestand is:
    return higallery_proxy_file($request);
}

