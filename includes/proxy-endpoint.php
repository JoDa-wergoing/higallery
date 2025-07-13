<?php
// Proxy endpoint voor HiDrive file requests
add_action('rest_api_init', function () {
    register_rest_route('higallery/v1', '/file', [
        'methods'  => 'GET',
        'callback' => 'higallery_proxy_file',
        'permission_callback' => '__return_true',
    ]);
});

function higallery_proxy_file($request) {
    $path = $request->get_param('path');

    if (empty($path)) {
        error_log('[HiGallery ERROR] Geen path opgegeven in file proxy');
        return new WP_Error('no_path', 'Geen pad opgegeven', ['status' => 400]);
    }

    $token = higallery_get_valid_access_token();
    if (!$token) {
        error_log('[HiGallery ERROR] Geen geldig access token in file proxy');
        return new WP_Error('no_token', 'Geen geldig access token', ['status' => 403]);
    }

    $url = 'https://api.hidrive.strato.com/2.1/file?path=' . $path;

    error_log('[HiGallery DEBUG] Proxy naar HiDrive URL: ' . $url);

    $response = wp_remote_get($url, [
        'timeout' => 20,
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
        ],
    ]);

    if (is_wp_error($response)) {
        error_log('[HiGallery ERROR] WP_Error in proxy: ' . $response->get_error_message());
        return new WP_Error('api_error', 'Fout bij ophalen bestand', ['status' => 502]);
    }

    $code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);

    error_log('[HiGallery DEBUG] Proxy response code: ' . $code);

    if ($code !== 200) {
        error_log('[HiGallery ERROR] Fout response van HiDrive: ' . $code . ' Body: ' . $body);
        return new WP_Error('hidrive_error', 'HiDrive gaf een foutmelding terug', [
            'status' => $code,
            'body' => $body,
        ]);
    }

    $content_type = wp_remote_retrieve_header($response, 'content-type') ?: 'application/octet-stream';
    $content_length = wp_remote_retrieve_header($response, 'content-length') ?: strlen($body);

    error_log('[HiGallery DEBUG] Proxy Content-Type: ' . $content_type);
    error_log('[HiGallery DEBUG] Proxy Content-Length: ' . $content_length);

    // Return response with clean headers
    return new WP_REST_Response($body, 200, [
        'Content-Type'   => $content_type,
        'Content-Length' => $content_length,
        'Cache-Control'  => 'public, max-age=3600',
        'Accept-Ranges'  => 'bytes',
    ]);
}
