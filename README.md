# HiGallery

HiGallery is a WordPress plugin that displays photo albums stored on **HiDrive** directly in your WordPress site.

It uses a **secure server-side proxy**, ensuring that access tokens are never exposed to visitors.

## Features

- Browse HiDrive folders as photo albums
- Secure server-side access
- Thumbnail support
- Efficient caching (ETag, browser cache, transients)
- WordPress coding standards compliant
- No media import into WordPress required

## Requirements

- WordPress 6.0+
- PHP 8.0+
- HiDrive account with API access

## Installation

1. Download the plugin zip
2. Upload via **Plugins → Add New → Upload Plugin**
3. Activate the plugin
4. Configure settings under **Settings → HiGallery**

## Usage

Basic shortcode:

```shortcode
[higallery path="/users/yourname/photoalbums"]

Security model

HiDrive access tokens are stored server-side only

Images are streamed through WordPress REST endpoints

Tokens are never exposed in HTML, JavaScript, or URLs

Development notes

REST endpoints are used for file and thumbnail proxying

Thumbnails are cached to reduce HiDrive API calls

Fully compatible with WordPress Plugin Checker

License

GPL v2 or later
© 2025

