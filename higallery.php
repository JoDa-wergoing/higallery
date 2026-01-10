<?php
/**
 * Plugin Name: HiGallery
 * Plugin URI:  https://wergoing.com/foto-album
 * Description: Description: Show your STRATO HiDrive photo folders as secure WordPress photo albums without importing images.
 * Version:     1.2.0
 * Author:      weRgoing JoDa
 * Author URI:  https://wergoing.com
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Text Domain: higallery
 * Domain Path: /languages
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

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
require_once HIGALLERY_PLUGIN_DIR . 'includes/all-albums-shortcode.php';
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
        '2.0.0',
        true
    );

    wp_enqueue_script(
        'higallery-photoswipe',
        HIGALLERY_PLUGIN_URL . 'assets/js/higallery-photoswipe.js',
        ['photoswipe-lightbox'],
        '1.2.0',
        true
);
    
    wp_enqueue_script(
        'higallery-lazyload',
        HIGALLERY_PLUGIN_URL . 'assets/js/higallery-lazyload.js',
        [],
        '1.2.0',
        true
    );
});
