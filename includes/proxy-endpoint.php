<?php
/**
 * HiGallery Proxy Endpoint
 *
 * Streams files from HiDrive via a WordPress REST endpoint.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'rest_api_init', function () {
    register_rest_route(
        'higallery/v1',
        '/file',
        [
            'methods'             => 'GET',
            'callback'            => 'higallery_proxy_file',
            'permission_callback' => '__return_true', // Public by design (documented)
        ]
    );
} );

/**
 * REST callback to proxy a file from HiDrive.
 *
 * @param WP_REST_Request $request
 * @return void|WP_Error
 */
function higallery_proxy_file( WP_REST_Request $request ) {

    // Sanitize input parameters.
    $raw_path = $request->get_param( 'path' );
    $path     = sanitize_textarea_field( (string) wp_unslash( $raw_path ) );

    if ( empty( $path ) ) {
        return new WP_Error(
            'higallery_invalid_path',
            __( 'Invalid or missing path parameter.', 'higallery' ),
            [ 'status' => 400 ]
        );
    }

    // Enforce root-folder validation (must exist in lower-level code).
    if ( function_exists( 'higallery_is_path_allowed' ) && ! higallery_is_path_allowed( $path ) ) {
        return new WP_Error(
            'higallery_forbidden_path',
            __( 'Access to this path is not allowed.', 'higallery' ),
            [ 'status' => 403 ]
        );
    }

    $token = higallery_get_valid_access_token();
    if ( ! $token ) {
        return new WP_Error(
            'higallery_no_token',
            __( 'No valid HiDrive access token available.', 'higallery' ),
            [ 'status' => 401 ]
        );
    }

    // Fetch file from HiDrive (implementation-specific).
    $response = higallery_hidrive_get_file( $path, $token );
    if ( is_wp_error( $response ) ) {
        return $response;
    }

    $body = wp_remote_retrieve_body( $response );
    $mime = wp_remote_retrieve_header( $response, 'content-type' );

    if ( empty( $body ) ) {
        return new WP_Error(
            'higallery_empty_response',
            __( 'Empty response from HiDrive.', 'higallery' ),
            [ 'status' => 502 ]
        );
    }

    if ( ! $mime ) {
        $mime = 'application/octet-stream';
    }

    // Output headers for binary stream.
    header( 'Content-Type: ' . $mime );
    header( 'Content-Length: ' . strlen( $body ) );
    header( 'Cache-Control: public, max-age=300' );

    // Binary output must not be escaped.
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Binary file stream (image bytes)
    echo $body;
    exit;
}
