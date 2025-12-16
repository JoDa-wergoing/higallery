## 1.1.0
- Added nonce verification for higallery_path navigation.
- Removed debug error_log() calls (repo compliance).

## 1.0.9
- Verwijderd: `Update URI` header (Plugin Directory compliance).
- Verwijderd: `load_plugin_textdomain()` (WordPress.org laadt vertalingen automatisch).
- Proxy-endpoint: `unlink()` vervangen door `wp_delete_file()`; `readfile()` vervangen door WP_Filesystem.
- Proxy-endpoint: PHPCS-annotaties voor binary output (geen escaping op image bytes).

## 1.0.7
- Cleanup: remove backup translation files with invalid names for WordPress.org.
- Metadata: update readme headers (Tested up to, Tags, Contributors).


## 1.0.4
- Fix: thumbnail proxy gebruikte een ongeldige HiDrive thumbnail URL waardoor er teruggevallen werd op full-size images.
## 1.0.3
- Fix: thumbnails gebruiken nu het juiste HiDrive endpoint (/file/thumbnail) met width/height, zodat album-overzicht echte thumbnails toont.

## 1.0.8
- Security: admin settings page output escaping (esc_html__/esc_attr/esc_url) om Plugin Check errors te voorkomen.
- Security: sanitization toegevoegd aan register_setting() (client id/secret, root folder, test mode, thumbnail size).

# HiGallery changelog

## 1.0.2
- Security: access token niet meer in HTML/IMG URLs; alles via server-side REST proxy
- Added: /higallery/v1/thumb endpoint (thumbnail proxy met fallback)
- Fix: shortcode + Gutenberg block gebruiken nu proxy URLs

## 1.0.1
- Fix: veilige REST proxy output voor binary bestanden (geen set_body; direct streamen)
- Security: path-validatie en beperking tot ingestelde root folder
- Compat: Range header ondersteuning

WordPress plugin.
By JoDa and Jake.
HiGallery integrates your HiDrive storage with WordPress. You can view albums and photos in a full-screen lightbox viewer (PhotoSwipe).  
The plugin uses a secure OAuth2 connection to HiDrive and supports both shortcodes and Gutenberg blocks.

## v0.9
First workable version without (known) bugs

- Added: PhotoSwipe lightbox integration for image display
- Added: assets/js/photoswipe-init.js script
- Improved: Albums show their names with spaces (no more %20)
- Improved: Root folder path uses the configured value by default (e.g. /users/jfdaam/albums)
- Retained: Full original directory structure and functionality
- Fix: Full PhotoSwipe retention + root folder + space fixes
- Fix: Building file URL with esc_url instead of esc_attr to show correct thumbnails
- Improved: Robust API client with better logging and fallback
- Correct path construction for files in gallery
- PhotoSwipe images now work with full path + token
- Robust distinction between albums and files
- Add multi language

- ## v0.9.1
- FIX: Removed encoding/decoding from core php. Now only in api-client
## 1.0.5
- Fix: HiDrive thumbnail endpoint URL opgebouwd zonder truncatie; thumbnails renderen weer i.p.v. full-size fallback.
