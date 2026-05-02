# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

## [1.2.0] - 2026-05-02

### Added
- CodeMirror-powered CSS editor support for the custom CSS admin setting
- JavaScript synchronization between the CodeMirror instance and the saved textarea value

### Changed
- Refactor search shell style enqueueing to reduce duplication in plugin bootstrap logic
- Replace the tracked deployment script with a committed `deploy.example.sh` template for local setup
- Expand ignored local-only deployment and session artifacts in Git

## [1.1.0] - 2026-03-24

### Added
- Cache manager for search result caching with configurable TTL
- AJAX handler for async search operations without page reloads
- Uninstall handler for clean plugin removal

### Changed
- Refactor loader architecture with improved ES integration
- Enhance query service with better result formatting
- Improve admin settings sanitization
- Update build workflow for improved releases

### Fixed
- JavaScript assets updated for new search features

## [1.0.0] - 2025-01-01

### Added
- AJAX-powered search with live results and no page reload
- Responsive grid layout with configurable 1–5 columns
- Content-type toggle pills to filter between all results and products
- WooCommerce integration with in-stock product filtering
- Optional featured image display for general posts and products
- Configurable excerpt display with adjustable word count
- 26 admin color picker settings covering all UI elements
- Custom CSS field for additional style overrides
- Transient-based search result caching (30-second TTL)
- Plugin-provided search results template (overrides theme template)
- `[alynt_es_search]` shortcode with button and icon display modes
- Settings page under Settings > Enhanced Search
- Automatic cache clear on plugin deactivation and uninstall
- Full uninstall cleanup (options, user meta, post meta, transients)
