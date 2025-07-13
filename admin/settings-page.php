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
        <h1><?php echo __('HiGallery admin page','higallery'); ?> </h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('higallery_settings');
            do_settings_sections('higallery-settings');
            submit_button();
            ?>
        </form>
        <hr>
        <h2><?php echo __('OAuth2 Connection','higallery'); ?></h2>
        <p><?php echo __('Click the button below to connect to your HiDrive account:','higallery'); ?></p>
        <?php
        $auth_url = higallery_get_oauth_authorize_url();
        echo '<a href="' . esc_url($auth_url) . '" class="button button-primary">'. __('Connect to HiDrive','higallery') .'</a>';
        ?>
    </div>
    <?php
}

add_action('admin_init', function () {
    register_setting('higallery_settings', 'higallery_client_id');
    register_setting('higallery_settings', 'higallery_client_secret');
    register_setting('higallery_settings', 'higallery_root_folder');
    register_setting('higallery_settings', 'higallery_test_mode');
    register_setting('higallery_settings', 'higallery_thumbnail_size');

    add_settings_section('higallery_main', __('Configuration','higallery'), null, 'higallery-settings');

    add_settings_field('higallery_client_id', 'Client ID', function () {
        $value = esc_attr(get_option('higallery_client_id'));
        echo "<input type='text' name='higallery_client_id' value='$value' class='regular-text' />";
    }, 'higallery-settings', 'higallery_main');

    add_settings_field('higallery_client_secret', 'Client Secret', function () {
        $value = esc_attr(get_option('higallery_client_secret'));
        echo "<input type='text' name='higallery_client_secret' value='$value' class='regular-text' />";
    }, 'higallery-settings', 'higallery_main');

    add_settings_field('higallery_root_folder', __('HiGallery root folder','higallery'), function () {
        $value = esc_attr(get_option('higallery_root_folder', '/'));
        echo "<input type='text' name='higallery_root_folder' value='$value' class='regular-text' />";
    }, 'higallery-settings', 'higallery_main');

    add_settings_field('higallery_thumbnail_size', __('Thumbnail width (px)','higallery'), function () {
        $value = esc_attr(get_option('higallery_thumbnail_size', '150'));
        echo "<input type='number' name='higallery_thumbnail_size' value='$value' min='50' max='1000' class='small-text' /> px";
    }, 'higallery-settings', 'higallery_main');
    
    add_settings_field('higallery_test_mode', __('Test mode','higallery'), function () {
        $value = get_option('higallery_test_mode');
        echo "<input type='checkbox' name='higallery_test_mode' value='1'" . checked(1, $value, false) . " />";
        echo "<p class='description'>" . __('Use demo albums (without HiDrive connection)','higallery') . "</p>";
    }, 'higallery-settings', 'higallery_main');


});
