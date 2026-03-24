<?php
/**
 * Search settings service for Alynt Enhanced Search.
 *
 * @package    Alynt_Enhanced_Search
 * @subpackage Alynt_Enhanced_Search/includes
 * @since      1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Manages retrieval of plugin settings with defaults.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */
class Alynt_ES_Search_Settings {

	/**
	 * Returns the default values for all plugin settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array Default settings key-value pairs.
	 */
    public static function get_defaults() {
        return array(
            'post_types' => array('post', 'page', 'product'),
            'results_per_page' => 12,
            'show_excerpt' => true,
            'excerpt_length' => 20,
            'max_columns' => 3,
            'show_featured_images_general' => true,
            'show_featured_images_products' => true,
            'search_icon_color' => '#333333',
            'search_page_bg_color' => '#ffffff',
            'main_title_text_color' => '#333333',
            'toggle_pill_bg_color' => '#f0f0f0',
            'toggle_pill_text_color' => '#666666',
            'toggle_pill_active_bg_color' => '#007cba',
            'toggle_pill_active_text_color' => '#ffffff',
            'result_card_border_color' => '#e0e0e0',
            'card_title_text_color' => '#333333',
            'card_excerpt_container_bg_color' => '#f9f9f9',
            'card_excerpt_text_color' => '#666666',
            'category_border_color' => '#cccccc',
            'category_text_color' => '#666666',
            'category_bg_color' => '#f9f9f9',
            'product_image_bg_color' => '#ffffff',
            'result_card_bg_color' => '#ffffff',
            'pagination_item_bg_color' => '#f0f0f0',
            'pagination_item_text_color' => '#333333',
            'pagination_current_bg_color' => '#007cba',
            'pagination_current_text_color' => '#ffffff',
            'pagination_item_border_color' => '#e0e0e0',
            'search_form_border_color' => '#e0e0e0',
            'search_form_bg_color' => '#ffffff',
            'search_submit_bg_color' => '#007cba',
            'search_submit_bg_hover_color' => '#005a87',
            'search_submit_text_color' => '#ffffff',
            'search_submit_text_hover_color' => '#ffffff',
            'custom_css' => ''
        );
    }

	/**
	 * Returns the current plugin settings merged with defaults.
	 *
	 * @since 1.0.0
	 *
	 * @return array Current plugin settings.
	 */
    public static function get_settings() {
        $settings = get_option('alynt_es_settings', array());

        if (!is_array($settings)) {
            $settings = array();
        }

        return wp_parse_args($settings, self::get_defaults());
    }

	/**
	 * Checks whether WooCommerce is active.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if WooCommerce is active, false otherwise.
	 */
    public static function is_woocommerce_enabled() {
        $settings = self::get_settings();

        return class_exists('WooCommerce') && in_array('product', $settings['post_types'], true);
    }
}
