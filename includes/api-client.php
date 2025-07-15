<?php

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
