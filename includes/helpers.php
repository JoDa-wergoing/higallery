<?php

if (!function_exists('higallery_clean_path')) {
    function higallery_clean_path($path) {
        error_log('[HiGallery DEBUG] clean_path input: ' . $path);
        $decoded = rawurldecode($path);
        error_log('[HiGallery DEBUG] clean_path output: ' . $decoded);
        return $decoded;
    }
}

if (!function_exists('higallery_encode_path')) {
    function higallery_encode_path($path) {
        $clean = higallery_clean_path($path);
        error_log('[HiGallery DEBUG] encode_path clean: ' . $clean);
        $encoded = rawurlencode($clean);
        $final = str_replace('%2F', '/', $encoded);
        error_log('[HiGallery DEBUG] encode_path final: ' . $final);
        return $final;
    }
}

if (!function_exists('higallery_sanitize_path_for_url')) {
    function higallery_sanitize_path_for_url($path) {
        error_log('[HiGallery DEBUG] sanitize_path_for_url input: ' . $path);
        $sanitized = higallery_encode_path($path);
        error_log('[HiGallery DEBUG] sanitize_path_for_url output: ' . $sanitized);
        return $sanitized;
    }
}
?>
