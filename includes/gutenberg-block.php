<?php
add_action('enqueue_block_editor_assets', function () {
    wp_enqueue_script(
        'higallery-block',
        plugin_dir_url(__FILE__) . '../assets/js/higallery-block.js',
        [ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-api-fetch' ],
        true
    );
});

add_action('init', function () {
    register_block_type('higallery/block', [
        'editor_script'   => 'higallery-block',
        'render_callback' => 'higallery_render_block',
        'attributes'      => [
            'albums' => [
                'type'    => 'array',
                'default' => [],
                'items'   => [ 'type' => 'string' ],
            ],
        ],
    ]);
});

function higallery_render_block($attributes) {
    $albums = isset($attributes['albums']) ? array_map('sanitize_text_field', (array) $attributes['albums']) : [];
    if (empty($albums)) {
        return '<p>' . __('No albums selected.','higallery') .'</p>';
    }
    return do_shortcode('[higallery albums="' . esc_attr(implode(';', $albums)) . '"]');
}

add_action('rest_api_init', function () {
    register_rest_route('higallery/v1', '/albums', [
        'methods'             => 'GET',
        'callback'            => 'higallery_rest_get_albums',
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        },
    ]);
});

function higallery_rest_get_albums() {
    $token = higallery_get_valid_access_token();
    if (!$token) {
        return new WP_Error('no_token', __('No valid access token.','higallery'), ['status' => 403]);
    }
    $root = get_option('higallery_root_folder', '/');
    $api_response = higallery_api_get_folders($root, $token);
    if (empty($api_response['albums'])) {
        return [];
    }
    $albums = array_map(function ($album) {
        return [
            'name' => $album['name'],
            'path' => $album['path'],
        ];
    }, $api_response['albums']);
    return $albums;
}