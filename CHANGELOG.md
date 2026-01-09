All notable changes to this project will be documented in this file.

This project follows Semantic Versioning.

[1.2.0] – 2026-01-09

### Changed
- Internal code cleanup in preparation for WordPress best practices.
- Version and metadata consistency restored across plugin, assets, and documentation.
- Caching behavior clarified and documented (TTL 5 minutes).

### Fixed
- Added missing `ABSPATH` guards to prevent direct access to included PHP files.
- Corrected minor inconsistencies in plugin headers.
- Prepared hardcoded frontend and admin strings for internationalization.

### Security
- Confirmed that all settings changes are handled through standard WordPress mechanisms.
- Explicitly limited the scope of the public proxy endpoint to the configured root folder.

### Documentation
- Updated the readme with correct version information.
- Clarified caching behavior and related expectations.


[1.1.0] – 2025-12-16

### Added
Improved album handling with configurable cover images per album
Enhanced REST API endpoint stability for image delivery
Additional validation for incoming REST and proxy requests

### Changed
Refactored internal request routing for better maintainability
Improved separation between file system access and presentation logic
Code cleanup to better align with WordPress coding standards

### Fixed
Fixed issues where images were listed but not rendered after endpoint changes
Improved handling of file paths containing spaces and special characters

### Security
Strengthened nonce verification for REST and form-based requests