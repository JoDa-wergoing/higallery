<?php

function higallery_get_oauth_authorize_url() {
    $client_id = get_option('higallery_client_id');
    if (empty($client_id)) {
        error_log('[HiGallery OAuth] Client ID missing. Check your settings on the admin page.');
        return '#';
    }

    $redirect_uri = higallery_get_redirect_uri();
    $scopes = 'all';

    $state = wp_generate_password(12, false);
    set_transient('higallery_oauth_state_' . $state, time(), 300); 

    $query = http_build_query([
        'response_type' => 'code',
        'client_id'     => $client_id,
        'redirect_uri'  => $redirect_uri,
        'scope'         => $scopes,
        'state'         => $state,
    ]);

    return 'https://my.hidrive.com/oauth2/authorize?' . $query;
}

function higallery_get_redirect_uri() {
    return home_url('/wp-json/higallery/oauth/callback');
}

function higallery_get_token($code) {
    $client_id = get_option('higallery_client_id');
    $client_secret = get_option('higallery_client_secret');
    $redirect_uri = higallery_get_redirect_uri();

    $response = wp_remote_post('https://my.hidrive.com/oauth2/token', [
        'timeout' => 15,
        'body' => [
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => $redirect_uri,
            'client_id'     => $client_id,
            'client_secret' => $client_secret,
        ]
    ]);

    if (is_wp_error($response)) {
        error_log('[HiGallery OAuth] WP_Error on token retrieval: ' . $response->get_error_message());
        return false;
    }

    $http_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    if ($http_code !== 200) {
        error_log("[HiGallery OAuth] Error getting token: HTTP $http_code - $body");
        return false;
    }

    $data = json_decode($body, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('[HiGallery OAuth] JSON decode error getting token: ' . json_last_error_msg());
        return false;
    }

    update_option('higallery_access_token', $data['access_token']);
    if (!empty($data['refresh_token'])) {
        update_option('higallery_refresh_token', $data['refresh_token']);
    }
    update_option('higallery_token_expires', time() + (int) $data['expires_in']);

    return $data['access_token'];
}

function higallery_refresh_token() {
    $client_id = get_option('higallery_client_id');
    $client_secret = get_option('higallery_client_secret');
    $refresh_token = get_option('higallery_refresh_token');

    if (empty($client_id) || empty($client_secret) || empty($refresh_token)) {
        error_log('[HiGallery OAuth] Refresh token or client data is missing');
        return false;
    }

    $response = wp_remote_post('https://my.hidrive.com/oauth2/token', [
        'timeout' => 15,
        'body' => [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refresh_token,
            'client_id'     => $client_id,
            'client_secret' => $client_secret,
        ]
    ]);

    if (is_wp_error($response)) {
        error_log('[HiGallery OAuth] WP_Error on refresh: ' . $response->get_error_message());
        return false;
    }

    $http_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    if ($http_code !== 200) {
        error_log("[HiGallery OAuth] Error on refresh: HTTP $http_code - $body");
        return false;
    }

    $data = json_decode($body, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('[HiGallery OAuth] JSON decode error on refresh: ' . json_last_error_msg());
        return false;
    }

    update_option('higallery_access_token', $data['access_token']);
    if (!empty($data['refresh_token'])) {
        update_option('higallery_refresh_token', $data['refresh_token']);
    }
    update_option('higallery_token_expires', time() + (int) $data['expires_in']);

    return $data['access_token'];
}

function higallery_get_valid_access_token() {
    $access_token = get_option('higallery_access_token');
    $expires = get_option('higallery_token_expires', 0);

    if ($access_token && time() < $expires) {
        return $access_token;
    }

    return higallery_refresh_token();
}
?>