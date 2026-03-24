<?php
/**
 * Plugin activator for Alynt Enhanced Search.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles plugin activation tasks.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */
class Alynt_ES_Activator {
	/**
	 * Runs plugin activation tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function activate() {
		require_once ALYNT_ES_PLUGIN_DIR . 'includes/class-search-settings.php';

		add_option( 'alynt_es_settings', Alynt_ES_Search_Settings::get_defaults() );
		flush_rewrite_rules();
	}
}
