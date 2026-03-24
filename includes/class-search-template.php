<?php
/**
 * Search template bootstrap for Alynt Enhanced Search.
 *
 * @package    Alynt_Enhanced_Search
 * @subpackage Alynt_Enhanced_Search/includes
 * @since      1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Bootstrap class for the search template loader and style manager.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */
class Alynt_ES_Search_Template {

    private $loader;
    private $style_manager;

	/**
	 * Constructor. Instantiates the template loader and style manager.
	 *
	 * @since 1.0.0
	 *
	 * @param Alynt_ES_Search_Template_Loader|null $loader        Template loader instance.
	 * @param Alynt_ES_Search_Style_Manager|null   $style_manager Style manager instance.
	 */
	public function __construct($loader = null, $style_manager = null) {
        $this->loader = $loader ? $loader : new Alynt_ES_Search_Template_Loader();
        $this->style_manager = $style_manager ? $style_manager : new Alynt_ES_Search_Style_Manager();
    }

	/**
	 * Returns the current plugin settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array Current plugin settings.
	 */
	public static function get_settings() {
        return Alynt_ES_Search_Settings::get_settings();
    }

	/**
	 * Checks whether WooCommerce is active.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if WooCommerce is active, false otherwise.
	 */
	public static function is_woocommerce_enabled() {
        return Alynt_ES_Search_Settings::is_woocommerce_enabled();
    }
}
