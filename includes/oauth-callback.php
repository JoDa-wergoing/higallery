<?php

add_action('rest_api_init', function () {
    register_rest_route('higallery', '/oauth/callback', [
        'methods'  => 'GET',
        'callback' => 'higallery_handle_oauth_callback',
        'permission_callback' => '__return_true', 
    ]);
});

function higallery_handle_oauth_callback($request) {
    $code = $request->get_param('code');
    $state = $request->get_param('state');

    if (empty($code) || empty($state)) {
        error_log('[HiGallery OAuth Callback] Missing code or state');
        return higallery_callback_redirect('Code or state missing.', 'error');
    }

    if (!get_transient('higallery_oauth_state_' . $state)) {
        error_log('[HiGallery OAuth Callback] Invalid or expired request (state)');
        return higallery_callback_redirect('Invalid or expired request (state).', 'error');
    }

    delete_transient('higallery_oauth_state_' . $state);

    $token = higallery_get_token($code);
    if (!$token) {
        return higallery_callback_redirect('Error retrieving access token. See log.', 'error');
    }

    return higallery_callback_redirect('HiDrive connection successful!', 'success');
}

function higallery_callback_redirect($message, $type = 'success') {
    $url = admin_url('admin.php?page=higallery-settings');
    $url = add_query_arg([
        'higallery_msg'  => urlencode($message),
        'higallery_type' => $type,
    ], $url);

    return new WP_REST_Response([
        'redirect' => $url
    ], 302, ['Location' => $url]);
}