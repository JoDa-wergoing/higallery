<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Haal alle album-covers op.
 *
 * Structuur:
 * [
 *   '/root/album1' => '/root/album1/foto1.jpg',
 *   '/root/album2' => '/root/album2/cover.png',
 * ]
 */
function higallery_get_album_covers() {
    $covers = get_option( 'higallery_album_covers', [] );
    if ( ! is_array( $covers ) ) {
        $covers = [];
    }
    return $covers;
}

/**
 * Zet de cover van één album.
 */
function higallery_set_album_cover( $album_path, $image_path ) {
    $album_path = sanitize_text_field( $album_path );
    $image_path = sanitize_text_field( $image_path );

    $covers = higallery_get_album_covers();
    $covers[ $album_path ] = $image_path;

    update_option( 'higallery_album_covers', $covers, false );
}
/**
 * REST route registreren voor het instellen van een album-cover.
 */
add_action( 'rest_api_init', function () {
    register_rest_route(
        'higallery/v1',
        '/album-cover',
        [
            'methods'             => 'POST',
            'callback'            => 'higallery_rest_set_album_cover',
            'permission_callback' => function () {
                // Alleen admins / beheerders
                return current_user_can( 'manage_options' );
            },
        ]
    );
} );

/**
 * Callback voor het instellen van de album-cover.
 */
function higallery_rest_set_album_cover( WP_REST_Request $request ) {
    $album_path = $request->get_param( 'album_path' );
    $image_path = $request->get_param( 'image_path' );

    if ( empty( $album_path ) || empty( $image_path ) ) {
        return new WP_REST_Response(
            [ 'success' => false, 'message' => 'album_path of image_path ontbreekt.' ],
            400
        );
    }

    higallery_set_album_cover( $album_path, $image_path );

    return new WP_REST_Response(
        [
            'success'    => true,
            'album_path' => $album_path,
            'image_path' => $image_path,
        ],
        200
    );
}
