<?php
/**
 * HiGallery uninstall cleanup.
 *
 * @package HiGallery
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$higallery_option_keys = array(
	'higallery_client_id',
	'higallery_client_secret',
	'higallery_access_token',
	'higallery_refresh_token',
	'higallery_token_expires',
	'higallery_root_folder',
	'higallery_album_covers',
	'higallery_thumbnail_size',
	'higallery_test_mode',
);

foreach ( $higallery_option_keys as $higallery_key ) {
	delete_option( $higallery_key );
	delete_site_option( $higallery_key );
}
