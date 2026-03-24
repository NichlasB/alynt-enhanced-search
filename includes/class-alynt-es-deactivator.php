<?php
/**
 * Plugin deactivator for Alynt Enhanced Search.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles plugin deactivation tasks.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */
class Alynt_ES_Deactivator {
	/**
	 * Runs plugin deactivation tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function deactivate() {
		require_once ALYNT_ES_PLUGIN_DIR . 'includes/class-alynt-es-search-cache-manager.php';

		Alynt_ES_Search_Cache_Manager::clear_transients();
		flush_rewrite_rules();
	}
}
