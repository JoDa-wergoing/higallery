<?php
/**
 * HiGallery admin settings page.
 *
 * @package HiGallery
 */

add_action( 'admin_menu', function () {
	add_options_page(
		__( 'HiGallery', 'higallery' ),          // Page title
		__( 'HiGallery', 'higallery' ),          // Menu title
		'manage_options',                        // Capability
		'higallery-settings',                    // Menu slug
		'higallery_render_settings_page'         // Callback
	);
} );

function higallery_render_settings_page() {
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'HiGallery settings', 'higallery' ); ?></h1>

		<form method="post" action="options.php">
			<?php
			settings_fields( 'higallery_settings' );
			do_settings_sections( 'higallery-settings' );
			submit_button();
			?>
		</form>

		<hr>

		<h2><?php echo esc_html__( 'OAuth2 connection', 'higallery' ); ?></h2>
		<p><?php echo esc_html__( 'Connect HiGallery to your HiDrive account:', 'higallery' ); ?></p>
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
 * Sanitize helpers
 */

function higallery_sanitize_root_folder( $value ) {
	$value = is_string( $value ) ? sanitize_text_field( $value ) : '';

	if ( '' === $value ) {
		return '/';
	}

	if ( '/' !== $value[0] ) {
		$value = '/' . $value;
	}

	if ( false !== strpos( $value, '..' ) ) {
		return '/';
	}

	return rtrim( $value, '/' );
}

function higallery_sanitize_checkbox( $value ) {
	return empty( $value ) ? 0 : 1;
}

function higallery_sanitize_thumbnail_size( $value ) {
	$value = absint( $value );
	return max( 50, min( 1000, $value ) );
}

/**
 * Sanitize client secret:
 * - keep stored value if input is empty
 * - keep stored value if input consists only of '*' with same length
 * - otherwise store new value
 */
function higallery_sanitize_client_secret( $input ) {
	$input  = is_string( $input ) ? trim( $input ) : '';
	$stored = (string) get_option( 'higallery_client_secret', '' );

	if ( '' === $input ) {
		return $stored;
	}

	if (
		$stored &&
		strlen( $input ) === strlen( $stored ) &&
		preg_match( '/^\*+$/', $input )
	) {
		return $stored;
	}

	return sanitize_text_field( $input );
}

add_action( 'admin_init', function () {

	/**
	 * Settings registration
	 */

	register_setting(
		'higallery_settings',
		'higallery_client_id',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	register_setting(
		'higallery_settings',
		'higallery_client_secret',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'higallery_sanitize_client_secret',
		)
	);

	register_setting(
		'higallery_settings',
		'higallery_root_folder',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'higallery_sanitize_root_folder',
		)
	);

	register_setting(
		'higallery_settings',
		'higallery_thumbnail_size',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'higallery_sanitize_thumbnail_size',
		)
	);

	register_setting(
		'higallery_settings',
		'higallery_test_mode',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'higallery_sanitize_checkbox',
		)
	);

	/**
	 * Settings section
	 */

	add_settings_section(
		'higallery_main',
		esc_html__( 'Configuration', 'higallery' ),
		null,
		'higallery-settings'
	);

	/**
	 * Fields
	 */

	add_settings_field(
		'higallery_client_id',
		esc_html__( 'Client ID', 'higallery' ),
		function () {
			$value = get_option( 'higallery_client_id', '' );
			printf(
				'<input type="text" name="higallery_client_id" value="%s" class="regular-text" />',
				esc_attr( $value )
			);
		},
		'higallery-settings',
		'higallery_main'
	);

	add_settings_field(
		'higallery_client_secret',
		esc_html__( 'Client Secret', 'higallery' ),
		function () {
			$stored  = (string) get_option( 'higallery_client_secret', '' );
			$display = $stored ? str_repeat( '*', strlen( $stored ) ) : '';

			printf(
				'<input type="password" name="higallery_client_secret" value="%s" class="regular-text" autocomplete="new-password" placeholder="%s" />',
				esc_attr( $display ),
				esc_attr__( 'Enter new secret', 'higallery' )
			);

			if ( $stored ) {
				echo '<p class="description">' .
					esc_html__( 'The client secret is stored securely. Enter a new value to replace it.', 'higallery' ) .
				'</p>';
			}
		},
		'higallery-settings',
		'higallery_main'
	);

	add_settings_field(
		'higallery_root_folder',
		esc_html__( 'HiGallery root folder', 'higallery' ),
		function () {
			$value = get_option( 'higallery_root_folder', '/' );
			printf(
				'<input type="text" name="higallery_root_folder" value="%s" class="regular-text" />',
				esc_attr( $value )
			);
		},
		'higallery-settings',
		'higallery_main'
	);

	add_settings_field(
		'higallery_thumbnail_size',
		esc_html__( 'Thumbnail width (px)', 'higallery' ),
		function () {
			$value = get_option( 'higallery_thumbnail_size', 150 );
			printf(
				'<input type="number" name="higallery_thumbnail_size" value="%s" min="50" max="1000" class="small-text" /> px',
				esc_attr( $value )
			);
		},
		'higallery-settings',
		'higallery_main'
	);

	add_settings_field(
		'higallery_test_mode',
		esc_html__( 'Test mode', 'higallery' ),
		function () {
			$value = (int) get_option( 'higallery_test_mode', 0 );
			printf(
				'<label><input type="checkbox" name="higallery_test_mode" value="1" %s /> %s</label>',
				checked( 1, $value, false ),
				esc_html__( 'Use demo albums without HiDrive connection', 'higallery' )
			);
		},
		'higallery-settings',
		'higallery_main'
	);

} );
