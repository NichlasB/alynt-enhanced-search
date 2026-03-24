<?php
/**
 * Search style manager for Alynt Enhanced Search.
 *
 * @package    Alynt_Enhanced_Search
 * @subpackage Alynt_Enhanced_Search/public
 * @since      1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Manages dynamic CSS generation and cache-control headers for search pages.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */
class Alynt_ES_Search_Style_Manager {

	/**
	 * Constructor. Registers wp_head and wp_enqueue_scripts hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
        add_action('send_headers', array($this, 'add_cache_headers'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_dynamic_styles'), 20);
    }

	/**
	 * Outputs cache-control HTTP headers on search pages to ensure fresh results.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_cache_headers() {
        if (!is_search()) {
            return;
        }

        if (headers_sent($file, $line)) {
            error_log(sprintf('[Alynt Enhanced Search] Could not send cache headers because output started in %s on line %d.', basename($file), $line));
            return;
        }

        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
    }

	/**
	 * Enqueues inline dynamic CSS styles generated from plugin settings on search pages.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_dynamic_styles() {
        if (!is_search()) {
            return;
        }

        $css = $this->build_dynamic_css(Alynt_ES_Search_Settings::get_settings());

        if ($css !== '') {
            wp_add_inline_style('alynt-es-search-shell', $css);
        }
    }

    private function build_dynamic_css($settings) {
        $rule_map = $this->get_dynamic_rule_map();
        $css = '';

        foreach ($rule_map as $setting_key => $rules) {
            if (empty($settings[$setting_key])) {
                continue;
            }

            foreach ($rules as $rule) {
                $css .= $rule['selector'] . ' { ' . $rule['property'] . ': ' . esc_attr($settings[$setting_key]) . ' !important; }';
            }
        }

        if (!empty($settings['custom_css'])) {
            $css .= wp_strip_all_tags($settings['custom_css']);
        }

        return $css;
    }

    private function get_dynamic_rule_map() {
        return array(
            'search_icon_color' => array(
                array('selector' => '.ct-header .alynt-es-search-icon', 'property' => 'color')
            ),
            'search_page_bg_color' => array(
                array('selector' => '.alynt-es-search-page', 'property' => 'background-color')
            ),
            'main_title_text_color' => array(
                array('selector' => '.alynt-es-main-title', 'property' => 'color')
            ),
            'toggle_pill_bg_color' => array(
                array('selector' => '.alynt-es-toggle-pill', 'property' => 'background-color')
            ),
            'toggle_pill_text_color' => array(
                array('selector' => '.alynt-es-toggle-pill', 'property' => 'color')
            ),
            'toggle_pill_active_bg_color' => array(
                array('selector' => '.alynt-es-toggle-pill.active', 'property' => 'background-color')
            ),
            'toggle_pill_active_text_color' => array(
                array('selector' => '.alynt-es-toggle-pill.active', 'property' => 'color')
            ),
            'result_card_border_color' => array(
                array('selector' => '.alynt-es-result-card', 'property' => 'border-color'),
                array('selector' => '.alynt-es-card-image', 'property' => 'border-color')
            ),
            'card_title_text_color' => array(
                array('selector' => '.alynt-es-card-title', 'property' => 'color')
            ),
            'card_excerpt_container_bg_color' => array(
                array('selector' => '.alynt-es-card-excerpt-container', 'property' => 'background-color')
            ),
            'card_excerpt_text_color' => array(
                array('selector' => '.alynt-es-card-excerpt', 'property' => 'color')
            ),
            'category_border_color' => array(
                array('selector' => '.alynt-es-card-category', 'property' => 'border-color')
            ),
            'category_text_color' => array(
                array('selector' => '.alynt-es-card-category', 'property' => 'color')
            ),
            'category_bg_color' => array(
                array('selector' => '.alynt-es-card-category', 'property' => 'background-color')
            ),
            'product_image_bg_color' => array(
                array('selector' => '.alynt-es-card-image.product-image', 'property' => 'background-color')
            ),
            'result_card_bg_color' => array(
                array('selector' => '.alynt-es-result-card', 'property' => 'background-color')
            ),
            'pagination_item_bg_color' => array(
                array('selector' => '.alynt-es-pagination-item', 'property' => 'background-color')
            ),
            'pagination_item_text_color' => array(
                array('selector' => '.alynt-es-pagination-item', 'property' => 'color')
            ),
            'pagination_current_bg_color' => array(
                array('selector' => '.alynt-es-pagination-item.current', 'property' => 'background-color')
            ),
            'pagination_current_text_color' => array(
                array('selector' => '.alynt-es-pagination-item.current', 'property' => 'color')
            ),
            'pagination_item_border_color' => array(
                array('selector' => '.alynt-es-pagination-item', 'property' => 'border-color')
            ),
            'search_form_border_color' => array(
                array('selector' => '.alynt-es-search-form', 'property' => 'border-color')
            ),
            'search_form_bg_color' => array(
                array('selector' => '.alynt-es-search-form', 'property' => 'background-color')
            ),
            'search_submit_bg_color' => array(
                array('selector' => '.alynt-es-search-submit', 'property' => 'background-color')
            ),
            'search_submit_bg_hover_color' => array(
                array('selector' => '.alynt-es-search-submit:hover', 'property' => 'background-color')
            ),
            'search_submit_text_color' => array(
                array('selector' => '.alynt-es-search-submit', 'property' => 'color')
            ),
            'search_submit_text_hover_color' => array(
                array('selector' => '.alynt-es-search-submit:hover', 'property' => 'color')
            )
        );
    }
}
