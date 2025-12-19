<?php
/**
 * HiGallery
 *
 * @package HiGallery
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

function higallery_shortcode($atts) {
    $default_root = get_option('higallery_root_folder', '/');
    $thumb_size = get_option('higallery_thumbnail_size', 300);

    $atts = shortcode_atts([
        'path' => $default_root,
        'albums' => ''
    ], $atts);

/**    $path = isset($_GET['higallery_path']) ? $_GET['higallery_path'] : $atts['path']; **/

    $path = $atts['path'];

if ( isset($_GET['higallery_path']) ) {
    $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
    if ( $nonce && wp_verify_nonce($nonce, 'higallery_browse') ) {
        $path = sanitize_text_field(wp_unslash($_GET['higallery_path']));
    }
}
    
    $selected_albums = array_filter(array_map('trim', explode(';', $atts['albums'])));

    $token = higallery_get_valid_access_token();
    if (!$token) {
        return '<p>' . __('Unable to load gallery: no valid access token.','higallery') . '</p>';
    }

    $api_response = higallery_api_get_folders($path, $token);
    if (empty($api_response) || is_wp_error($api_response)) {
        return '<p>'. __('Cannot load gallery. Check the settings or API connection.','higallery') . ' </p>';
    }

    $output = '<div class="higallery-wrapper" style="display: flex; flex-wrap: wrap; gap: 20px;">';

    if (!empty($api_response['images'])) {
        $output .= '<div class="pswp-gallery" style="display: flex; flex-wrap: wrap; gap: 10px;">';
        foreach ($api_response['images'] as $image) {
            $file_url = rest_url('higallery/v1/file') . '?path=' . rawurlencode($image['path']);
            $thumb_url = rest_url('higallery/v1/thumb') . '?path=' . rawurlencode($image['path']) . '&width=' . intval($thumb_size);

            $output .= '<a href="' . esc_url($file_url) . '" data-pswp-width="1600" data-pswp-height="1067">';
            $output .= '<img src="' . esc_url($thumb_url) . '" alt="' . esc_attr($image['name']) . '" />';
            $output .= '</a>';
        }
        $output .= '</div>';
    } elseif (!empty($api_response['albums'])) {
        foreach ($api_response['albums'] as $album) {

            if (!empty($selected_albums) && !in_array($album['name'], $selected_albums, true)) {
                continue;
            }

            $sub_path = $album['path'];
            $link = add_query_arg([
                'higallery_path' => $sub_path,
                '_wpnonce'       => wp_create_nonce('higallery_browse'),
            ], get_permalink());

            $output .= '<a href="' . esc_url($link) . '" style="width: 120px; text-align: center; text-decoration: none; color: inherit;">';
            $output .= '<div style="font-size: 48px; color: #555;">';
            $output .= '<svg viewBox="0 0 40 40" width="48" height="48" fill="none" xmlns="http://www.w3.org/2000/svg">'
                     . '<path d="M36.25 8.599h-15L16.25 4H3.75c-.995 0-1.948.394-2.652 1.094A3.73 3.73 0 0 0 0 7.737v24.526a3.73 3.73 0 0 0 1.098 2.643A3.757 3.757 0 0 0 3.75 36h32.5c.995 0 1.948-.394 2.652-1.094A3.73 3.73 0 0 0 40 32.264V12.335a3.73 3.73 0 0 0-1.098-2.642A3.757 3.757 0 0 0 36.25 8.6Z" fill="currentColor"/>'
                     . '</svg>';
            $output .= '</div>';
            $output .= '<div style="margin-top: 8px; font-size: 14px;">' . esc_html($album['name']) . '</div>';
            $output .= '</a>';
        }

        if ($output === '<div class="higallery-wrapper" style="display: flex; flex-wrap: wrap; gap: 20px;">') {
            $output .= '<p>'. __('No albums found.','higallery') . '</p>';
        }
    } else {
        $output .= '<p>' . __('No photos found in this album.','higallery') . '</p>';
    }

    $output .= '</div>';
    return $output;
}
?>