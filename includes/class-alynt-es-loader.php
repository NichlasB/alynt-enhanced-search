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
		self::load_shared();
		self::load_frontend();
		self::load_ajax();
		self::load_admin();
	}

	/**
	 * Loads shared dependencies required in every runtime context.
	 *
	 * @return void
	 */
	public static function load_shared() {
		self::require_files(
			array(
				'includes/class-search-settings.php',
				'includes/class-alynt-es-search-cache-manager.php',
			)
		);
	}

	/**
	 * Loads frontend-only dependencies.
	 *
	 * @return void
	 */
	public static function load_frontend() {
		self::require_files(
			array(
				'public/class-shortcode.php',
				'public/class-alynt-es-search-template-loader.php',
				'public/class-search-style-manager.php',
				'includes/class-search-template.php',
			)
		);
	}

	/**
	 * Loads AJAX-only dependencies.
	 *
	 * @return void
	 */
	public static function load_ajax() {
		self::require_files(
			array(
				'includes/class-search-result-formatter.php',
				'includes/class-pagination-builder.php',
				'includes/class-search-query-service.php',
				'includes/class-alynt-es-ajax-handler.php',
			)
		);
	}

	/**
	 * Loads admin-only dependencies.
	 *
	 * @return void
	 */
	public static function load_admin() {
		self::require_files(
			array(
				'admin/class-admin-settings-fields.php',
				'admin/class-admin-settings-sanitizer.php',
				'admin/class-admin-settings-registry.php',
				'admin/class-admin-settings-page.php',
				'includes/class-admin-settings.php',
			)
		);
	}

	/**
	 * Requires a list of plugin files.
	 *
	 * @param array $files Relative file paths.
	 *
	 * @return void
	 */
	private static function require_files( $files ) {
		foreach ( $files as $file ) {
			require_once ALYNT_ES_PLUGIN_DIR . $file;
		}
	}
}
