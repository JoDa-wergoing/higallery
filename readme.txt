=== HiGallery ===
Contributors: jfdaam
Tags: gallery, photos, hidrive, albums, media
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 8.0
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display photo albums from HiDrive in WordPress using a simple shortcode.

== Description ==

HiGallery displays photo albums stored on HiDrive directly inside WordPress.

The plugin connects to HiDrive using a secure server-side proxy, ensuring that access tokens are never exposed to visitors.
Folders are displayed as albums, images are loaded on demand, and thumbnails are generated efficiently via the HiDrive API.

HiGallery is ideal for photographers, bloggers, and organisations that want to keep their photos on HiDrive while presenting them cleanly on a WordPress website.

Key features:
* Display HiDrive folders as photo albums in WordPress
* Secure server-side access (no tokens in browser)
* Thumbnail generation via HiDrive API
* Folder-based navigation
* Optimised caching and performance
* No media import into WordPress

== Installation ==

1. Upload the `higallery` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to *Settings → HiGallery* and configure your HiDrive connection
4. Add the gallery to a page using the shortcode

== Usage ==

Basic usage:

[higallery path="/users/yourname/photoalbums"]

Optional attributes:

* `path` – Root folder to display
* `thumb_size` – Thumbnail size in pixels (default: 150)

== Frequently Asked Questions ==

= Are my HiDrive access tokens visible to visitors? =
No. All HiDrive communication is handled server-side.

= Does this plugin upload photos to WordPress? =
No. Photos remain stored on HiDrive and are streamed when needed.

= Is caching supported? =
Yes. Thumbnails and images are cached using HTTP cache headers and WordPress transients.

== Screenshots ==

1. Album overview
2. Folder navigation
3. Thumbnail grid

== Changelog ==

= 1.1.0 =
* WordPress.org compliance fixes
* Nonce verification added
* Debug logging removed

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.1.0 =
Recommended update for WordPress.org compliance and security improvements.
