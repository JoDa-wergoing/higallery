All notable changes to this project will be documented in this file.

This project follows Semantic Versioning.

[1.1.0] – 2025-12-16
Added

Improved album handling with configurable cover images per album

Enhanced REST API endpoint stability for image delivery

Additional validation for incoming REST and proxy requests

Changed

Refactored internal request routing for better maintainability

Improved separation between file system access and presentation logic

Code cleanup to better align with WordPress coding standards

Fixed

Fixed issues where images were listed but not rendered after endpoint changes

Improved handling of file paths containing spaces and special characters

Security

Strengthened nonce verification for REST and form-based requests