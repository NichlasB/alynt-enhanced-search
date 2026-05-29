=== Alynt Enhanced Search ===
Contributors: alynt
Tags: search, ajax search, woocommerce, shortcode
Requires at least: 6.0
Tested up to: 6.8.2
Requires PHP: 7.4
Stable tag: 1.2.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A minimalistic, AJAX-powered search plugin with grid layout and WooCommerce integration.

== Description ==

Alynt Enhanced Search adds an AJAX-powered search experience to WordPress with a responsive results grid, content-type filtering, and optional WooCommerce product support.

= Features =

* AJAX-powered search results without page reloads
* Responsive grid layout with configurable columns
* Content filtering between general content and products
* WooCommerce integration for product search
* Configurable excerpts, thumbnails, and colors
* Shortcode support with `[alynt_es_search]`

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/alynt-enhanced-search` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to Settings > Enhanced Search to configure the plugin.

== Frequently Asked Questions ==

= How do I configure this plugin? =

Go to Settings > Enhanced Search in the WordPress admin.

== Changelog ==

= 1.2.1 =
* Fixed the header shortcode search icon size on screens 999px wide and below.

= 1.1.0 =
* Added cache manager for search result caching
* Added AJAX handler for async search operations
* Added uninstall handler for clean plugin removal
* Refactored loader architecture with improved ES integration
* Enhanced query service with better result formatting
* Improved admin settings sanitization
* Updated build workflow for releases

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
