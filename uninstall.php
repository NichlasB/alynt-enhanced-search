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

if ( function_exists( 'current_user_can' ) && ! current_user_can( 'activate_plugins' ) ) {
	return;
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

function alynt_es_remove_site_plugin_options() {
	delete_option( 'alynt_es_settings' );
}

function alynt_es_delete_site_transient_rows() {
	global $wpdb;

	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_alynt_es_%'" );
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_alynt_es_%'" );
}

function alynt_es_cleanup_current_site() {
	alynt_es_remove_site_plugin_options();
	Alynt_ES_Search_Cache_Manager::clear_transients();
	alynt_es_delete_site_transient_rows();
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

	if ( is_multisite() ) {
		$site_ids = get_sites( array( 'fields' => 'ids' ) );

		foreach ( $site_ids as $site_id ) {
			switch_to_blog( (int) $site_id );
			alynt_es_cleanup_current_site();
			restore_current_blog();
		}
	} else {
		alynt_es_cleanup_current_site();
	}

	alynt_es_clear_caches();
}

alynt_es_uninstall_plugin();
