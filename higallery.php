<?php
/**
 * Plugin Name: HiGallery
 * Description: Show Strato HiDrive albums in WordPress with a fullscreen lightbox viewer. With secure OAuth2 connection.
 * Version: 0.9.0
 * Author: JoDa & Jake 🥷
 * License: GPL2
 */

add_action('plugins_loaded', function() {
    load_plugin_textdomain('higallery', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

define('HIGALLERY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HIGALLERY_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once HIGALLERY_PLUGIN_DIR . 'includes/oauth.php';
require_once HIGALLERY_PLUGIN_DIR . 'includes/oauth-callback.php';
require_once HIGALLERY_PLUGIN_DIR . 'includes/api-client.php';
require_once HIGALLERY_PLUGIN_DIR . 'includes/gallery-shortcode.php';
require_once HIGALLERY_PLUGIN_DIR . 'includes/helpers.php';
require_once HIGALLERY_PLUGIN_DIR . 'admin/settings-page.php';
require_once HIGALLERY_PLUGIN_DIR . 'includes/proxy-endpoint.php';
require_once HIGALLERY_PLUGIN_DIR . 'includes/gutenberg-block.php';

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'photoswipe-css',
        HIGALLERY_PLUGIN_URL . 'assets/photoswipe/photoswipe.css',
        [],
        '5.3.8'
    );
wp_enqueue_script(
    'higallery-js',
    plugin_dir_url(__FILE__) . 'assets/js/higallery-block.js',
    ['wp-i18n'],
    '1.0',
    true
);

wp_set_script_translations(
    'higallery-js',
    'higallery',
    plugin_dir_path(__FILE__) . '/languages'
);  
    wp_enqueue_script(
        'photoswipe-core',
        HIGALLERY_PLUGIN_URL . 'assets/photoswipe/photoswipe.umd.min.js',
        [],
        '5.3.8',
        true
    );

    wp_enqueue_script(
        'photoswipe-js',
        HIGALLERY_PLUGIN_URL . 'assets/photoswipe/photoswipe-lightbox.umd.min.js',
        [ 'photoswipe-core' ],
        '5.3.8',
        true
    );

    wp_enqueue_script(
        'photoswipe-init',
        HIGALLERY_PLUGIN_URL . 'assets/js/photoswipe-init.js',
        [ 'photoswipe-js' ],
        '1.0.0',
        true
    );
    wp_enqueue_script(
        'higallery-lazyload',
        HIGALLERY_PLUGIN_URL . 'assets/js/higallery-lazyload.js',
        [],
        true
    );
});

add_action('enqueue_block_editor_assets', function () {
    wp_register_script(
        'higallery-block',
        HIGALLERY_PLUGIN_URL . 'assets/js/higallery-block.js',
        [ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-api-fetch' ],
        '1.0.0',
        true
    );
    wp_enqueue_script('higallery-block');

    wp_set_script_translations(
        'higallery-block',
        'higallery',
        HIGALLERY_PLUGIN_DIR . 'languages'
    );
});