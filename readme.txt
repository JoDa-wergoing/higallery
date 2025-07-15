=== HiGallery ===
Contributors: Jake & JoDa
Tags: gallery, hidrive, lightbox, photoswipe, albums, photogallery
Requirements at least: 6.0
Tested up to: 6.8.1
Stable tag: 0.9.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display HiDrive albums and photos in WordPress with a full-screen PhotoSwipe lightbox. Secure OAuth2 connection and Gutenberg block support.

== Description ==

HiGallery integrates your HiDrive storage with WordPress. You can display albums and photos in a full-screen lightbox viewer (PhotoSwipe).  
The plugin uses a secure OAuth2 connection to HiDrive and supports both shortcodes and Gutenberg blocks.

**Key features:**  
- Show albums and photos from your HiDrive account.  
- Fullscreen display with PhotoSwipe lightbox.  
- Gutenberg block for easy integration.  
- Support for album selection by shortcode or block.  
- OAuth2 authorization and token management.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/higallery/` directory, or install from the WordPress plugin screen.
2. Activate the plugin via the 'Plugins' menu in WordPress.
3. Go to 'HiGallery' in the WordPress admin menu.
4. Enter your HiDrive Client ID and Client Secret and set the root folder.
5. Click "Connect to HiDrive" and complete the OAuth authorization.
6. Place a shortcode or block on your page:
   - Shortcode example: `[higallery albums="Sri Lanka 2023"]`
   - Without albums attribute, the plugin shows all albums from the root folder.

==Usage==

**Shortcode:**  
Use `[higallery]` to show all albums from the root folder.  
Use `[higallery albums="Album Name 1,Album Name 2"]` to show specific albums.  
Example:  
`[higallery albums="Photoshop Express"]`

**Gutenberg block:**  
1. Add the HiGallery block to your page.  
2. Select the desired albums in the block sidebar.  
3. Publish or update your page.

== Frequently Asked Questions ==

= I don't see any albums or photos? =  
Check that your root folder is set up correctly and that you have a valid access token. Also check your debug.log for error messages.

= How can I style the lightbox? =  
The plugin uses PhotoSwipe 5. You can add your own CSS or use PhotoSwipe themes.

== Changelog ==

= 0.9 =
This stable first pre-release
= 0.9.1 =
Cleaned up encoding/decoding api responses. Now only in api-client

== Todo ==

Make album photo cover
Modify the list of albums on ALL selection (no album list is everything)
