<?php
/**
 * Uninstall script for Alynt Enhanced Search.
 *
 * Runs when the plugin is deleted from the WordPress admin.
 * Removes plugin options and transient caches.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-alynt-es-search-cache-manager.php';

/**
 * Removes the plugin's options from the database.
 *
 * @since 1.0.0
 *
 * @return void
 */
function alynt_es_remove_plugin_options() {
	delete_option( 'alynt_es_settings' );
	delete_site_option( 'alynt_es_settings' );
}

/**
 * Clears plugin transients, object cache, OPcache, and rewrite rules on uninstall.
 *
 * @since 1.0.0
 *
 * @return void
 */
function alynt_es_clear_caches() {
	Alynt_ES_Search_Cache_Manager::clear_transients();

	wp_cache_flush();

	if ( function_exists( 'opcache_reset' ) ) {
		opcache_reset();
	}

	flush_rewrite_rules();
}

/**
 * Orchestrates the full plugin uninstall sequence.
 *
 * @since 1.0.0
 *
 * @return void
 */
function alynt_es_uninstall_plugin() {
	alynt_es_remove_plugin_options();
	alynt_es_clear_caches();
}

alynt_es_uninstall_plugin();
