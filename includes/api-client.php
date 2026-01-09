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

if ( ! defined( 'ABSPATH' ) ) { exit; }

function higallery_api_get_folders($path, $token) {
    if (empty($token)) {
        return [];
    }

    $path = (string) $path;
    $encoded_path = rawurlencode($path);

    // Ask HiDrive for image metadata (width/height) + exif (for orientation)
    $fields = implode(',', [
        'path',
        'name',
        'type',
        'members.path',
        'members.name',
        'members.type',
        'members.mime_type',
        'members.image.width',
        'members.image.height',
        'members.image.exif',
    ]);

    $url = 'https://api.hidrive.strato.com/2.1/dir?path=' . $encoded_path . '&fields=' . rawurlencode($fields);

    $response = wp_remote_get($url, [
        'timeout' => 20,
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ],
    ]);

    if (is_wp_error($response)) {
        return [];
    }

    $code = (int) wp_remote_retrieve_response_code($response);
    if ($code !== 200) {
        return [];
    }

    $body = wp_remote_retrieve_body($response);
    $json = json_decode($body, true);

    if (!is_array($json)) {
        return [];
    }

    $members = $json['members'] ?? [];
    if (!is_array($members)) {
        $members = [];
    }

    $albums = [];
    $images = [];

    foreach ($members as $entry) {
        if (!is_array($entry)) {
            continue;
        }

        // IMPORTANT: keys inside each member are name/path/type (not "members.name")
        $type = isset($entry['type']) ? (string) $entry['type'] : '';
        $name = isset($entry['name']) ? (string) $entry['name'] : '';
        $p    = isset($entry['path']) ? (string) $entry['path'] : '';

        // Skip malformed entries
        if ($type === '' || $name === '' || $p === '') {
            continue;
        }

        $entry_info = [
            'name' => urldecode($name),
            'type' => $type,
            'path' => urldecode($p),
        ];

        if (!empty($entry['mime_type'])) {
            $entry_info['mime_type'] = (string) $entry['mime_type'];
        }

        // Optional image metadata (for PhotoSwipe aspect ratio)
        if (!empty($entry['image']) && is_array($entry['image'])) {
            $w = !empty($entry['image']['width'])  ? (int) $entry['image']['width']  : 0;
            $h = !empty($entry['image']['height']) ? (int) $entry['image']['height'] : 0;

            // Try to read EXIF orientation if provided by HiDrive
            $orientation = 1;
            if (!empty($entry['image']['exif']) && is_array($entry['image']['exif'])) {
                $exif = $entry['image']['exif'];

                // Handle common possible keys
                if (isset($exif['Orientation'])) {
                    $orientation = (int) $exif['Orientation'];
                } elseif (isset($exif['orientation'])) {
                    $orientation = (int) $exif['orientation'];
                } elseif (isset($exif['EXIF:Orientation'])) {
                    $orientation = (int) $exif['EXIF:Orientation'];
                }
            }

            // EXIF orientations 5-8 imply 90/270 rotation => swap dimensions
            if ($w > 0 && $h > 0 && in_array($orientation, [5, 6, 7, 8], true)) {
                $tmp = $w; $w = $h; $h = $tmp;
            }

            if ($w > 0) $entry_info['width']  = $w;
            if ($h > 0) $entry_info['height'] = $h;
        }

        // Images
        if ($type === 'file' && preg_match('/\.(jpe?g|png|gif)$/i', $name)) {
            $images[] = $entry_info;
            continue;
        }

        // Albums (folders)
        if ($type === 'dir') {
            $albums[] = $entry_info;
            continue;
        }
    }

    return [
        'albums' => $albums,
        'images' => $images,
    ];
}
