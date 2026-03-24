<?php
/**
 * Admin settings registry for Alynt Enhanced Search.
 *
 * @package    Alynt_Enhanced_Search
 * @subpackage Alynt_Enhanced_Search/admin
 * @since      1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registers all plugin settings, sections, and fields with the WordPress Settings API.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */
class Alynt_ES_Admin_Settings_Registry {

    private $fields;
    private $sanitizer;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Alynt_ES_Admin_Settings_Fields    $fields    Settings fields renderer.
	 * @param Alynt_ES_Admin_Settings_Sanitizer $sanitizer Settings sanitizer.
	 */
	public function __construct($fields, $sanitizer) {
        $this->fields = $fields;
        $this->sanitizer = $sanitizer;
    }

	/**
	 * Registers the settings group, option name, and all setting sections and fields.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init_settings() {
        register_setting('alynt_es_settings_group', 'alynt_es_settings', array($this->sanitizer, 'sanitize_settings'));

        $this->register_general_settings();
        $this->register_display_settings();
        $this->register_color_settings();
        $this->register_css_settings();
    }

    private function register_general_settings() {
        add_settings_section('alynt_es_general_section', __('General Settings', 'alynt-enhanced-search'), array($this, 'general_section_callback'), 'alynt-enhanced-search');
        add_settings_field('post_types', __('Post Types to Include', 'alynt-enhanced-search'), array($this->fields, 'post_types_callback'), 'alynt-enhanced-search', 'alynt_es_general_section');
        add_settings_field('results_per_page', __('Results Per Page', 'alynt-enhanced-search'), array($this->fields, 'results_per_page_callback'), 'alynt-enhanced-search', 'alynt_es_general_section', array('label_for' => 'alynt_es_results_per_page'));
        add_settings_field('max_columns', __('Maximum Columns', 'alynt-enhanced-search'), array($this->fields, 'max_columns_callback'), 'alynt-enhanced-search', 'alynt_es_general_section', array('label_for' => 'alynt_es_max_columns'));
    }

    private function register_display_settings() {
        add_settings_section('alynt_es_display_section', __('Display Settings', 'alynt-enhanced-search'), array($this, 'display_section_callback'), 'alynt-enhanced-search');
        add_settings_field('show_excerpt', __('Show Excerpts', 'alynt-enhanced-search'), array($this->fields, 'show_excerpt_callback'), 'alynt-enhanced-search', 'alynt_es_display_section');
        add_settings_field('excerpt_length', __('Excerpt Length (words)', 'alynt-enhanced-search'), array($this->fields, 'excerpt_length_callback'), 'alynt-enhanced-search', 'alynt_es_display_section', array('label_for' => 'alynt_es_excerpt_length'));
        add_settings_field('show_featured_images_general', __('Show Featured Images (General Pages)', 'alynt-enhanced-search'), array($this->fields, 'show_featured_images_general_callback'), 'alynt-enhanced-search', 'alynt_es_display_section');
        add_settings_field('show_featured_images_products', __('Show Featured Images (Products)', 'alynt-enhanced-search'), array($this->fields, 'show_featured_images_products_callback'), 'alynt-enhanced-search', 'alynt_es_display_section');
    }

    private function register_color_settings() {
        add_settings_section('alynt_es_color_section', __('Color Settings', 'alynt-enhanced-search'), array($this, 'color_section_callback'), 'alynt-enhanced-search');

        $color_fields = array(
            'search_icon_color' => array('label' => __('Search Icon Color', 'alynt-enhanced-search'), 'callback' => 'search_icon_color_callback'),
            'search_page_bg_color' => array('label' => __('Search Page Background Color', 'alynt-enhanced-search'), 'callback' => 'search_page_bg_color_callback'),
            'main_title_text_color' => array('label' => __('Main Title Text Color', 'alynt-enhanced-search'), 'callback' => 'main_title_text_color_callback'),
            'toggle_pill_bg_color' => array('label' => __('Toggle Pill Background Color', 'alynt-enhanced-search'), 'callback' => 'toggle_pill_bg_color_callback'),
            'toggle_pill_text_color' => array('label' => __('Toggle Pill Text Color', 'alynt-enhanced-search'), 'callback' => 'toggle_pill_text_color_callback'),
            'toggle_pill_active_bg_color' => array('label' => __('Active Toggle Pill Background Color', 'alynt-enhanced-search'), 'callback' => 'toggle_pill_active_bg_color_callback'),
            'toggle_pill_active_text_color' => array('label' => __('Active Toggle Pill Text Color', 'alynt-enhanced-search'), 'callback' => 'toggle_pill_active_text_color_callback'),
            'result_card_border_color' => array('label' => __('Result Card & Image Border Color', 'alynt-enhanced-search'), 'callback' => 'result_card_border_color_callback'),
            'card_title_text_color' => array('label' => __('Card Title Text Color', 'alynt-enhanced-search'), 'callback' => 'card_title_text_color_callback'),
            'card_excerpt_container_bg_color' => array('label' => __('Card Excerpt Container Background Color', 'alynt-enhanced-search'), 'callback' => 'card_excerpt_container_bg_color_callback'),
            'card_excerpt_text_color' => array('label' => __('Card Excerpt Text Color', 'alynt-enhanced-search'), 'callback' => 'card_excerpt_text_color_callback'),
            'category_border_color' => array('label' => __('Category Border Color', 'alynt-enhanced-search'), 'callback' => 'category_border_color_callback'),
            'category_text_color' => array('label' => __('Category Text Color', 'alynt-enhanced-search'), 'callback' => 'category_text_color_callback'),
            'category_bg_color' => array('label' => __('Category Background Color', 'alynt-enhanced-search'), 'callback' => 'category_bg_color_callback'),
            'product_image_bg_color' => array('label' => __('Product Image Background Color', 'alynt-enhanced-search'), 'callback' => 'product_image_bg_color_callback'),
            'result_card_bg_color' => array('label' => __('Result Card Background Color', 'alynt-enhanced-search'), 'callback' => 'result_card_bg_color_callback'),
            'pagination_item_bg_color' => array('label' => __('Pagination Item Background Color', 'alynt-enhanced-search'), 'callback' => 'pagination_item_bg_color_callback'),
            'pagination_item_text_color' => array('label' => __('Pagination Item Text Color', 'alynt-enhanced-search'), 'callback' => 'pagination_item_text_color_callback'),
            'pagination_current_bg_color' => array('label' => __('Current Pagination Item Background Color', 'alynt-enhanced-search'), 'callback' => 'pagination_current_bg_color_callback'),
            'pagination_current_text_color' => array('label' => __('Current Pagination Item Text Color', 'alynt-enhanced-search'), 'callback' => 'pagination_current_text_color_callback'),
            'pagination_item_border_color' => array('label' => __('Pagination Item Border Color', 'alynt-enhanced-search'), 'callback' => 'pagination_item_border_color_callback'),
            'search_form_border_color' => array('label' => __('Search Form Border Color', 'alynt-enhanced-search'), 'callback' => 'search_form_border_color_callback'),
            'search_form_bg_color' => array('label' => __('Search Form Background Color', 'alynt-enhanced-search'), 'callback' => 'search_form_bg_color_callback'),
            'search_submit_bg_color' => array('label' => __('Search Submit Button Background Color', 'alynt-enhanced-search'), 'callback' => 'search_submit_bg_color_callback'),
            'search_submit_bg_hover_color' => array('label' => __('Search Submit Button Background Color (Hover)', 'alynt-enhanced-search'), 'callback' => 'search_submit_bg_hover_color_callback'),
            'search_submit_text_color' => array('label' => __('Search Submit Button Text Color', 'alynt-enhanced-search'), 'callback' => 'search_submit_text_color_callback'),
            'search_submit_text_hover_color' => array('label' => __('Search Submit Button Text Color (Hover)', 'alynt-enhanced-search'), 'callback' => 'search_submit_text_hover_color_callback')
        );

        foreach ($color_fields as $field => $config) {
            add_settings_field($field, $config['label'], array($this->fields, $config['callback']), 'alynt-enhanced-search', 'alynt_es_color_section', array('label_for' => 'alynt_es_' . $field));
        }
    }

    private function register_css_settings() {
        add_settings_section('alynt_es_css_section', __('Custom CSS', 'alynt-enhanced-search'), array($this, 'css_section_callback'), 'alynt-enhanced-search');
        add_settings_field('custom_css', __('Custom CSS Styles', 'alynt-enhanced-search'), array($this->fields, 'custom_css_callback'), 'alynt-enhanced-search', 'alynt_es_css_section', array('label_for' => 'alynt_es_custom_css'));
    }

	/**
	 * Renders the General settings section description.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function general_section_callback() {
        echo '<p>' . esc_html__('Configure which content types to include in search results and pagination settings.', 'alynt-enhanced-search') . '</p>';
    }

	/**
	 * Renders the Display settings section description.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function display_section_callback() {
        echo '<p>' . esc_html__('Control how search results are displayed to your visitors.', 'alynt-enhanced-search') . '</p>';
    }

	/**
	 * Renders the Color settings section description.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function color_section_callback() {
        echo '<p>' . esc_html__('Customize the colors of your search page elements. Use hex color codes (e.g., #ffffff for white).', 'alynt-enhanced-search') . '</p>';
    }

	/**
	 * Renders the Custom CSS settings section description.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function css_section_callback() {
        echo '<p>' . esc_html__('Add custom CSS to style the search page and results to match your theme.', 'alynt-enhanced-search') . '</p>';
    }
}
