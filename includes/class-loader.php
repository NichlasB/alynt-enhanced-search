<?php
/**
 * Dependency loader for Alynt Enhanced Search.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads plugin class dependencies.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */
class Alynt_ES_Loader {
	/**
	 * Loads all required plugin files.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function load() {
		$files = array(
			'includes/class-search-settings.php',
			'includes/class-search-cache-manager.php',
			'includes/class-search-result-formatter.php',
			'includes/class-pagination-builder.php',
			'includes/class-search-query-service.php',
			'public/class-shortcode.php',
			'includes/class-search-template.php',
			'includes/class-ajax-handler.php',
			'admin/class-admin-settings-fields.php',
			'admin/class-admin-settings-sanitizer.php',
			'admin/class-admin-settings-registry.php',
			'admin/class-admin-settings-page.php',
			'includes/class-admin-settings.php',
			'public/class-search-template-loader.php',
			'public/class-search-style-manager.php',
		);

		foreach ( $files as $file ) {
			require_once ALYNT_ES_PLUGIN_DIR . $file;
		}
	}
}
