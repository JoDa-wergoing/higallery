=== HiGallery ===
Contributors: JoDa weRgoing
Tags: gallery, photos, albums, hidrive
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 8.0
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A simple and secure way to display server-side photo folders as albums in WordPress.

== Description ==

HiGallery lets you display your STRATO HiDrive photo folders directly on your WordPress site as structured photo albums.

Unlike traditional gallery plugins, HiGallery does not import images into the WordPress Media Library. 
Images stay in HiDrive and are delivered securely to your site, which is ideal for large collections or photo archives that are managed outside WordPress.

HiGallery focuses on simplicity, performance, and security, following WordPress.org best practices.


== Features ==

Display HiDrive photo folders as albums using a shortcode

Automatic sub-album and image detection

Secure REST API for image delivery

Configurable album cover images

Lazy loading for improved performance

== Installation ==

Upload the higallery folder to the /wp-content/plugins/ directory

Activate the plugin through the "Plugins" menu in WordPress

Add the HiGallery shortcode to a page or post

== Frequently Asked Questions ==

= Does this plugin import images into the Media Library? =

No. HiGallery works directly with existing server-side folders.

= Is this plugin secure? =

Yes. HiGallery uses nonce verification and WordPress REST API best practices.

== Changelog ==

= 1.1.0 =

Improved album handling and cover image configuration

Refactored REST and proxy endpoints

Security and stability improvements

== Upgrade Notice ==

= 1.1.0 = This release improves stability, security, and internal structure. Update recommended.

== Third-party libraries ==

This plugin bundles PhotoSwipe (MIT License)
© Dmitry Semenov

== External Services ==

HiDrive is a registered product of STRATO AG.

This plugin connects to STRATO HiDrive to retrieve folder listings and serve images.

Endpoints used:
* https://my.hidrive.com/oauth2/authorize (OAuth authorization)
* https://my.hidrive.com/oauth2/token (OAuth token exchange/refresh)
* https://api.hidrive.strato.com/2.1/dir (folder listing)
* https://api.hidrive.strato.com/2.1/file (file retrieval)

Data sent:
* OAuth authorization codes/tokens (to obtain access to your HiDrive account)
* File and folder paths required to list albums and retrieve images

This service is provided by STRATO HiDrive and is subject to their terms and privacy policy.