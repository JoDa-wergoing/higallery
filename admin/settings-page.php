<?php
add_action('admin_menu', function () {
    add_menu_page(
        'HiGallery',
        'HiGallery',
        'manage_options',
        'higallery-settings',
        'higallery_render_settings_page',
        'dashicons-format-gallery'
    );
});

function higallery_render_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__( 'HiGallery admin page', 'higallery' ); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('higallery_settings');
            do_settings_sections('higallery-settings');
            submit_button();
            ?>
        </form>
        <hr>
        <h2><?php echo esc_html__( 'OAuth2 Connection', 'higallery' ); ?></h2>
        <p><?php echo esc_html__( 'Click the button below to connect to your HiDrive account:', 'higallery' ); ?></p>
        <?php
        $auth_url = higallery_get_oauth_authorize_url();
        printf(
            '<a href="%s" class="button button-primary">%s</a>',
            esc_url( $auth_url ),
            esc_html__( 'Connect to HiDrive', 'higallery' )
        );
        ?>
    </div>
    <?php
}

/**
 * Sanitize HiGallery root folder path.
 */
function higallery_sanitize_root_folder( $value ) {
    $value = is_string( $value ) ? $value : '';
    $value = sanitize_text_field( $value );
    // Normalize slashes and ensure leading slash.
    $value = str_replace( array( "\\\r", "\\\n", "\0" ), '', $value );
    if ( '' === $value ) {
        return '/';
    }
    if ( '/' !== $value[0] ) {
        $value = '/' . $value;
    }
    // Prevent traversal.
    if ( false !== strpos( $value, '..' ) ) {
        return '/';
    }
    return rtrim( $value, '/' );
}

/**
 * Sanitize thumbnail width.
 */
function higallery_sanitize_thumbnail_size( $value ) {
    $value = absint( $value );
    if ( $value < 50 ) {
        $value = 50;
    }
    if ( $value > 1000 ) {
        $value = 1000;
    }
    return $value;
}

/**
 * Sanitize checkbox.
 */
function higallery_sanitize_checkbox( $value ) {
    return empty( $value ) ? 0 : 1;
}

add_action('admin_init', function () {
    register_setting('higallery_settings', 'higallery_client_id', array(
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    register_setting('higallery_settings', 'higallery_client_secret', array(
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    register_setting('higallery_settings', 'higallery_root_folder', array(
        'sanitize_callback' => 'higallery_sanitize_root_folder',
    ) );
    register_setting('higallery_settings', 'higallery_test_mode', array(
        'sanitize_callback' => 'higallery_sanitize_checkbox',
    ) );
    register_setting('higallery_settings', 'higallery_thumbnail_size', array(
        'sanitize_callback' => 'higallery_sanitize_thumbnail_size',
    ) );

    add_settings_section('higallery_main', esc_html__( 'Configuration', 'higallery' ), null, 'higallery-settings');

    add_settings_field('higallery_client_id', esc_html__( 'Client ID', 'higallery' ), function () {
        $value = get_option( 'higallery_client_id', '' );
        printf(
            '<input type="text" name="higallery_client_id" value="%s" class="regular-text" />',
            esc_attr( $value )
        );
    }, 'higallery-settings', 'higallery_main');

    add_settings_field('higallery_client_secret', esc_html__( 'Client Secret', 'higallery' ), function () {
        $value = get_option( 'higallery_client_secret', '' );
        printf(
            '<input type="text" name="higallery_client_secret" value="%s" class="regular-text" />',
            esc_attr( $value )
        );
    }, 'higallery-settings', 'higallery_main');

    add_settings_field('higallery_root_folder', esc_html__( 'HiGallery root folder', 'higallery' ), function () {
        $value = get_option( 'higallery_root_folder', '/' );
        printf(
            '<input type="text" name="higallery_root_folder" value="%s" class="regular-text" />',
            esc_attr( $value )
        );
    }, 'higallery-settings', 'higallery_main');

    add_settings_field('higallery_thumbnail_size', esc_html__( 'Thumbnail width (px)', 'higallery' ), function () {
        $value = get_option( 'higallery_thumbnail_size', 150 );
        printf(
            '<input type="number" name="higallery_thumbnail_size" value="%s" min="50" max="1000" class="small-text" /> %s',
            esc_attr( $value ),
            esc_html__( 'px', 'higallery' )
        );
    }, 'higallery-settings', 'higallery_main');
    
    add_settings_field('higallery_test_mode', esc_html__( 'Test mode', 'higallery' ), function () {
        $value = (int) get_option( 'higallery_test_mode', 0 );
        printf(
            '<input type="checkbox" name="higallery_test_mode" value="1" %s />',
            checked( 1, $value, false )
        );
        printf(
            '<p class="description">%s</p>',
            esc_html__( 'Use demo albums (without HiDrive connection)', 'higallery' )
        );
    }, 'higallery-settings', 'higallery_main');


});
