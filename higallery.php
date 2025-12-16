<?php
/*
 * Plugin Name:       HiGallery
 * Plugin URI:        https://github.com/JoDa-wergoing/higallery
 * Description:       Show Strato HiDrive albums in WordPress with a fullscreen lightbox viewer. With secure OAuth2 connection.
 * Version: 1.1.0
 * Requires at least: 6.0
 * Requires PHP:      8.2
 * Author:            JoDa & Jake 🥷
 * Author URI:        https://wergoing.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       higallery
 * Domain Path:       /languages
 * Requires Plugins:  
 */


if ( ! defined('ABSPATH') ) {
    exit;
}

// Definieer een constante voor de menu-slug
if ( ! defined('HIGALLERY_MENU_SLUG') ) {
    define('HIGALLERY_MENU_SLUG', 'higallery-settings');
}


add_action('plugins_loaded', function() {
});

add_shortcode('higallery','higallery_shortcode');

define('HIGALLERY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HIGALLERY_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once HIGALLERY_PLUGIN_DIR . 'includes/oauth.php';
require_once HIGALLERY_PLUGIN_DIR . 'includes/oauth-callback.php';
require_once HIGALLERY_PLUGIN_DIR . 'includes/api-client.php';
require_once HIGALLERY_PLUGIN_DIR . 'includes/gallery-shortcode.php';
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
