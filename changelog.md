# HiGallery changelog
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