<?php
/**
 * Search cache manager for Alynt Enhanced Search.
 *
 * @package    Alynt_Enhanced_Search
 * @subpackage Alynt_Enhanced_Search/includes
 * @since      1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages the search results transient cache.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */
class Alynt_ES_Search_Cache_Manager {

	/**
	 * Option name used to track transient cache keys.
	 *
	 * @since 1.0.0
	 */
	const CACHE_KEYS_OPTION = 'alynt_es_cache_keys';
	const MAX_CACHE_KEYS    = 200;

	/**
	 * Generates a unique transient cache key for a search request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $search_query The search query string.
	 * @param string $search_type  The content type filter (e.g. 'all', 'products').
	 * @param int    $page         The current results page number.
	 *
	 * @return string MD5-based transient cache key prefixed with 'alynt_es_'.
	 */
	public static function get_cache_key( $search_query, $search_type, $page ) {
		return 'alynt_es_' . md5( $search_query . $search_type . $page );
	}

	/**
	 * Registers a cache key so it can be cleared later with WordPress transient APIs.
	 *
	 * @since 1.0.0
	 *
	 * @param string $cache_key Transient cache key.
	 *
	 * @return void
	 */
	public static function register_cache_key( $cache_key ) {
		$cache_keys = self::prune_cache_keys( self::get_cache_keys() );

		if ( in_array( $cache_key, $cache_keys, true ) ) {
			update_option( self::CACHE_KEYS_OPTION, $cache_keys, false );
			return;
		}

		$cache_keys[] = $cache_key;

		if ( count( $cache_keys ) > self::MAX_CACHE_KEYS ) {
			$stale_cache_keys = array_splice( $cache_keys, 0, count( $cache_keys ) - self::MAX_CACHE_KEYS );

			foreach ( $stale_cache_keys as $stale_cache_key ) {
				delete_transient( $stale_cache_key );
			}
		}

		update_option( self::CACHE_KEYS_OPTION, $cache_keys, false );
	}

	/**
	 * Deletes all plugin search transients using WordPress APIs.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function clear_transients() {
		$cache_keys = self::get_cache_keys();

		foreach ( $cache_keys as $cache_key ) {
			delete_transient( $cache_key );
		}

		delete_option( self::CACHE_KEYS_OPTION );
		delete_site_option( self::CACHE_KEYS_OPTION );
	}

	/**
	 * Returns the tracked transient cache keys.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private static function get_cache_keys() {
		$cache_keys = get_option( self::CACHE_KEYS_OPTION, array() );

		if ( ! is_array( $cache_keys ) ) {
			return array();
		}

		return $cache_keys;
	}

	/**
	 * Removes invalid or expired cache keys from the registry.
	 *
	 * @param array $cache_keys Registered cache keys.
	 *
	 * @return array
	 */
	private static function prune_cache_keys( $cache_keys ) {
		$active_cache_keys = array();

		foreach ( $cache_keys as $registered_cache_key ) {
			if ( ! is_string( $registered_cache_key ) || '' === $registered_cache_key ) {
				continue;
			}

			if ( false !== get_transient( $registered_cache_key ) ) {
				$active_cache_keys[] = $registered_cache_key;
			}
		}

		return $active_cache_keys;
	}
}
