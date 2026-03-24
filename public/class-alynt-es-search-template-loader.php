<?php
/**
 * Search template loader for Alynt Enhanced Search.
 *
 * @package    Alynt_Enhanced_Search
 * @subpackage Alynt_Enhanced_Search/public
 * @since      1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Overrides the WordPress search template with the plugin's custom template.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */
class Alynt_ES_Search_Template_Loader {

	/**
	 * Constructor. Registers the template_include filter.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'template_include', array( $this, 'load_search_template' ), 10, 1 );
	}

	/**
	 * Replaces the active theme's search template with the plugin's template.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Path to the current template file.
	 *
	 * @return string Path to the plugin's search template on search pages, original template otherwise.
	 */
	public function load_search_template( $template ) {
		if ( ! is_search() ) {
			return $template;
		}

		if ( ! apply_filters( 'alynt_es_override_search_template', true, $template ) ) {
			return $template;
		}

		$custom_template = ALYNT_ES_PLUGIN_DIR . 'templates/search.php';

		if ( file_exists( $custom_template ) ) {
			return $custom_template;
		}

		return $template;
	}
}
