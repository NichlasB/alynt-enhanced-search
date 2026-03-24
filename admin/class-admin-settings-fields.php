<?php
/**
 * Admin settings fields for Alynt Enhanced Search.
 *
 * @package    Alynt_Enhanced_Search
 * @subpackage Alynt_Enhanced_Search/admin
 * @since      1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Defines and renders all admin settings fields for the plugin options page.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */
class Alynt_ES_Admin_Settings_Fields {

	/**
	 * Renders the post types selection field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function post_types_callback() {
        $settings = Alynt_ES_Search_Settings::get_settings();
        $selected_types = isset($settings['post_types']) ? $settings['post_types'] : array('post', 'page', 'product');
        $post_types = get_post_types(array('public' => true), 'objects');

        echo '<fieldset>';
        foreach ($post_types as $post_type) {
            $checked = in_array($post_type->name, $selected_types, true) ? 'checked' : '';
            echo '<label>';
            echo '<input type="checkbox" name="alynt_es_settings[post_types][]" value="' . esc_attr($post_type->name) . '" ' . $checked . '> ';
            echo esc_html($post_type->label);
            echo '</label><br>';
        }
        echo '</fieldset>';
        echo '<p class="description">' . esc_html__('Select which post types should be included in search results. Pages, Posts, and Products are selected by default.', 'alynt-enhanced-search') . '</p>';
    }

	/**
	 * Renders the results per page number input field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function results_per_page_callback() {
        $this->render_number_field('results_per_page', 12, 1, 50, __('Number of search results to display per page.', 'alynt-enhanced-search'));
    }

	/**
	 * Renders the maximum columns select field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function max_columns_callback() {
        $settings = Alynt_ES_Search_Settings::get_settings();
        $value = isset($settings['max_columns']) ? $settings['max_columns'] : 3;

        echo '<select id="alynt_es_max_columns" name="alynt_es_settings[max_columns]">';
        for ($i = 1; $i <= 5; $i++) {
            $selected = ((int) $value === $i) ? 'selected' : '';
            echo '<option value="' . esc_attr($i) . '" ' . $selected . '>' . esc_html(
                sprintf(
                    /* translators: %d: number of columns. */
                    _n('%d column', '%d columns', $i, 'alynt-enhanced-search'),
                    $i
                )
            ) . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . esc_html__('Maximum number of columns in the results grid (1-5).', 'alynt-enhanced-search') . '</p>';
    }

	/**
	 * Renders the show excerpt checkbox field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function show_excerpt_callback() {
        $this->render_checkbox_field('show_excerpt', __('Display excerpts in search result cards', 'alynt-enhanced-search'));
    }

	/**
	 * Renders the excerpt length number input field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function excerpt_length_callback() {
        $this->render_number_field('excerpt_length', 20, 5, 100, __('Maximum number of words to display in excerpts.', 'alynt-enhanced-search'));
    }

	/**
	 * Renders the show featured images (general posts) checkbox field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function show_featured_images_general_callback() {
        $this->render_checkbox_field('show_featured_images_general', __('Show featured images for general pages (posts, pages, etc.)', 'alynt-enhanced-search'));
    }

	/**
	 * Renders the show featured images (products) checkbox field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function show_featured_images_products_callback() {
        $this->render_checkbox_field('show_featured_images_products', __('Show featured images for WooCommerce products', 'alynt-enhanced-search'));
    }

	/**
	 * Renders the search icon color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function search_icon_color_callback() { $this->render_color_field('search_icon_color', '#333333', __('Color for the search icon.', 'alynt-enhanced-search')); }

	/**
	 * Renders the search page background color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function search_page_bg_color_callback() { $this->render_color_field('search_page_bg_color', '#ffffff', __('Background color for the search page container (.alynt-es-search-page).', 'alynt-enhanced-search')); }

	/**
	 * Renders the main title text color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function main_title_text_color_callback() { $this->render_color_field('main_title_text_color', '#333333', __('Text color for the main search title (.alynt-es-main-title).', 'alynt-enhanced-search')); }

	/**
	 * Renders the toggle pill background color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function toggle_pill_bg_color_callback() { $this->render_color_field('toggle_pill_bg_color', '#f0f0f0', __('Background color for toggle pills (.alynt-es-toggle-pill).', 'alynt-enhanced-search')); }

	/**
	 * Renders the toggle pill text color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function toggle_pill_text_color_callback() { $this->render_color_field('toggle_pill_text_color', '#666666', __('Text color for toggle pills (.alynt-es-toggle-pill).', 'alynt-enhanced-search')); }

	/**
	 * Renders the active toggle pill background color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function toggle_pill_active_bg_color_callback() { $this->render_color_field('toggle_pill_active_bg_color', '#007cba', __('Background color for active toggle pills (.alynt-es-toggle-pill.active).', 'alynt-enhanced-search')); }

	/**
	 * Renders the active toggle pill text color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function toggle_pill_active_text_color_callback() { $this->render_color_field('toggle_pill_active_text_color', '#ffffff', __('Text color for active toggle pills (.alynt-es-toggle-pill.active).', 'alynt-enhanced-search')); }

	/**
	 * Renders the result card border color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function result_card_border_color_callback() { $this->render_color_field('result_card_border_color', '#e0e0e0', __('Border color for result cards and card images (.alynt-es-result-card, .alynt-es-card-image).', 'alynt-enhanced-search')); }

	/**
	 * Renders the card title text color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function card_title_text_color_callback() { $this->render_color_field('card_title_text_color', '#333333', __('Text color for card titles (.alynt-es-card-title).', 'alynt-enhanced-search')); }

	/**
	 * Renders the card excerpt container background color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function card_excerpt_container_bg_color_callback() { $this->render_color_field('card_excerpt_container_bg_color', '#f9f9f9', __('Background color for card excerpt container (.alynt-es-card-excerpt-container).', 'alynt-enhanced-search')); }

	/**
	 * Renders the card excerpt text color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function card_excerpt_text_color_callback() { $this->render_color_field('card_excerpt_text_color', '#666666', __('Text color for card excerpts (.alynt-es-card-excerpt).', 'alynt-enhanced-search')); }

	/**
	 * Renders the category badge border color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function category_border_color_callback() { $this->render_color_field('category_border_color', '#cccccc', __('Border color for category tags (.alynt-es-card-category).', 'alynt-enhanced-search')); }

	/**
	 * Renders the category badge text color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function category_text_color_callback() { $this->render_color_field('category_text_color', '#666666', __('Text color for category tags (.alynt-es-card-category).', 'alynt-enhanced-search')); }

	/**
	 * Renders the category badge background color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function category_bg_color_callback() { $this->render_color_field('category_bg_color', '#f9f9f9', __('Background color for category tags (.alynt-es-card-category).', 'alynt-enhanced-search')); }

	/**
	 * Renders the product image background color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function product_image_bg_color_callback() { $this->render_color_field('product_image_bg_color', '#ffffff', __('Background color for product images (.alynt-es-card-image.product-image).', 'alynt-enhanced-search')); }

	/**
	 * Renders the result card background color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function result_card_bg_color_callback() { $this->render_color_field('result_card_bg_color', '#ffffff', __('Background color for result cards (.alynt-es-result-card).', 'alynt-enhanced-search')); }

	/**
	 * Renders the pagination item background color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function pagination_item_bg_color_callback() { $this->render_color_field('pagination_item_bg_color', '#f0f0f0', __('Background color for pagination items (.alynt-es-pagination-item).', 'alynt-enhanced-search')); }

	/**
	 * Renders the pagination item text color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function pagination_item_text_color_callback() { $this->render_color_field('pagination_item_text_color', '#333333', __('Text color for pagination items (.alynt-es-pagination-item).', 'alynt-enhanced-search')); }

	/**
	 * Renders the active pagination item background color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function pagination_current_bg_color_callback() { $this->render_color_field('pagination_current_bg_color', '#007cba', __('Background color for current pagination item (.alynt-es-pagination-item.current).', 'alynt-enhanced-search')); }

	/**
	 * Renders the active pagination item text color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function pagination_current_text_color_callback() { $this->render_color_field('pagination_current_text_color', '#ffffff', __('Text color for current pagination item (.alynt-es-pagination-item.current).', 'alynt-enhanced-search')); }

	/**
	 * Renders the pagination item border color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function pagination_item_border_color_callback() { $this->render_color_field('pagination_item_border_color', '#e0e0e0', __('Border color for pagination items (.alynt-es-pagination-item).', 'alynt-enhanced-search')); }

	/**
	 * Renders the search form border color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function search_form_border_color_callback() { $this->render_color_field('search_form_border_color', '#e0e0e0', __('Border color for search form (.alynt-es-search-form).', 'alynt-enhanced-search')); }

	/**
	 * Renders the search form background color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function search_form_bg_color_callback() { $this->render_color_field('search_form_bg_color', '#ffffff', __('Background color for search form (.alynt-es-search-form).', 'alynt-enhanced-search')); }

	/**
	 * Renders the search submit button background color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function search_submit_bg_color_callback() { $this->render_color_field('search_submit_bg_color', '#007cba', __('Background color for search submit button (.alynt-es-search-submit).', 'alynt-enhanced-search')); }

	/**
	 * Renders the search submit button hover background color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function search_submit_bg_hover_color_callback() { $this->render_color_field('search_submit_bg_hover_color', '#005a87', __('Background color for search submit button on hover (.alynt-es-search-submit:hover).', 'alynt-enhanced-search')); }

	/**
	 * Renders the search submit button text color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function search_submit_text_color_callback() { $this->render_color_field('search_submit_text_color', '#ffffff', __('Text color for search submit button (.alynt-es-search-submit).', 'alynt-enhanced-search')); }

	/**
	 * Renders the search submit button hover text color picker field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function search_submit_text_hover_color_callback() { $this->render_color_field('search_submit_text_hover_color', '#ffffff', __('Text color for search submit button on hover (.alynt-es-search-submit:hover).', 'alynt-enhanced-search')); }

	/**
	 * Renders the custom CSS textarea field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function custom_css_callback() {
        $settings = Alynt_ES_Search_Settings::get_settings();
        $value = isset($settings['custom_css']) ? $settings['custom_css'] : '';

        echo '<textarea id="alynt_es_custom_css" name="alynt_es_settings[custom_css]" rows="10" cols="80" class="large-text code">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">' . esc_html__('Add custom CSS to style the search page and results. This CSS will be loaded on the search page only.', 'alynt-enhanced-search') . '</p>';
    }

    private function render_number_field($field, $default, $min, $max, $description) {
        $settings = Alynt_ES_Search_Settings::get_settings();
        $value = isset($settings[$field]) ? $settings[$field] : $default;

        echo '<input type="number" id="alynt_es_' . esc_attr($field) . '" name="alynt_es_settings[' . esc_attr($field) . ']" value="' . esc_attr($value) . '" min="' . esc_attr($min) . '" max="' . esc_attr($max) . '" class="small-text">';
        echo '<p class="description">' . esc_html($description) . '</p>';
    }

    private function render_checkbox_field($field, $label) {
        $settings = Alynt_ES_Search_Settings::get_settings();
        $checked = !empty($settings[$field]) ? 'checked' : '';

        echo '<label>';
        echo '<input type="checkbox" name="alynt_es_settings[' . esc_attr($field) . ']" value="1" ' . $checked . '> ';
        echo esc_html($label);
        echo '</label>';
    }

    private function render_color_field($field, $default, $description) {
        $settings = Alynt_ES_Search_Settings::get_settings();
        $value = isset($settings[$field]) ? $settings[$field] : $default;

        echo '<input type="text" id="alynt_es_' . esc_attr($field) . '" name="alynt_es_settings[' . esc_attr($field) . ']" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="' . esc_attr($default) . '">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[' . esc_attr($field) . ']" aria-hidden="true" tabindex="-1">';
        echo '<p class="description">' . esc_html($description) . '</p>';
    }
}
