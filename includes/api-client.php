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

function higallery_api_get_folders($path, $token) {
    if (empty($token)) {
        return [];
    }

    $encoded_path = rawurlencode($path);
    $fields = 'path,name,type,members.path,members.name,members.type';
    $url = 'https://api.hidrive.strato.com/2.1/dir?path=' . $encoded_path . '&fields=' . rawurlencode($fields);

    $response = wp_remote_get($url, [
        'timeout' => 15,
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
        ],
    ]);

    if (is_wp_error($response)) {
        return [];
    }

    $code = wp_remote_retrieve_response_code($response);
    if ($code !== 200) {
        return [];
    }

    $body = wp_remote_retrieve_body($response);
    $json = json_decode($body, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [];
    }

    $members = $json['members'] ?? [];
    $albums = [];
    $images = [];

    foreach ($members as $entry) {
        if (empty($entry['type'])) {
            continue;
        }

        $entry_info = [
            'name' => urldecode($entry['name']) ?? '',
            'type' => $entry['type'],
            'path' => urldecode($entry['path']) ?? '',
        ];
        
        if (
            $entry['type'] === 'file' &&
            preg_match('/\.(jpg|jpeg|png|gif)$/i', $entry['name'])
        ) {
            $images[] = $entry_info;
        } elseif ($entry['type'] === 'dir') {
            $albums[] = $entry_info;
        }
    }

    return [
        'albums' => $albums,
        'images' => $images,
    ];
}
?>
