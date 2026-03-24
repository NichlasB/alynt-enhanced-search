<?php
/**
 * Admin settings sanitizer for Alynt Enhanced Search.
 *
 * @package    Alynt_Enhanced_Search
 * @subpackage Alynt_Enhanced_Search/admin
 * @since      1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sanitizes plugin settings input before saving to the database.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */
class Alynt_ES_Admin_Settings_Sanitizer {

	/**
	 * Sanitizes all plugin settings fields from a submitted options form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input Raw input array from the settings form submission.
	 *
	 * @return array Sanitized settings array.
	 */
	public function sanitize_settings($input) {
        $sanitized = array();

        $sanitized['post_types'] = $this->sanitize_post_types($input);
        $sanitized['results_per_page'] = $this->sanitize_int($input, 'results_per_page', 12, 1, 50, __('Results per page must be between 1 and 50. The previous value was kept.', 'alynt-enhanced-search'));
        $sanitized['max_columns'] = $this->sanitize_int($input, 'max_columns', 3, 1, 5, __('Maximum columns must be between 1 and 5. The previous value was kept.', 'alynt-enhanced-search'));
        $sanitized['show_excerpt'] = !empty($input['show_excerpt']);
        $sanitized['show_featured_images_general'] = !empty($input['show_featured_images_general']);
        $sanitized['show_featured_images_products'] = !empty($input['show_featured_images_products']);
        $sanitized['excerpt_length'] = $this->sanitize_int($input, 'excerpt_length', 20, 5, 100, __('Excerpt length must be between 5 and 100 words. The previous value was kept.', 'alynt-enhanced-search'));

        foreach ($this->get_color_defaults() as $field => $default) {
            $sanitized[$field] = $this->sanitize_color($input, $field, $default);
        }

        $custom_css = isset($input['custom_css']) ? wp_strip_all_tags($input['custom_css']) : '';

        if ( preg_match( '/@import|expression\s*\(|javascript\s*:|behavior\s*:|binding\s*:|url\s*\(\s*["\']?\s*(?:https?:|data:|javascript:)/i', $custom_css ) ) {
            add_settings_error(
                'alynt_es_settings',
                'alynt_es_custom_css_unsafe',
                __( 'Custom CSS contains disallowed patterns (@import, expression, javascript:, url with external/data URI). The previous value was kept.', 'alynt-enhanced-search' ),
                'error'
            );
            $custom_css = Alynt_ES_Search_Settings::get_settings()['custom_css'];
        }

        if ( mb_strlen( $custom_css ) > 10000 ) {
            add_settings_error(
                'alynt_es_settings',
                'alynt_es_custom_css_too_long',
                __( 'Custom CSS must be 10,000 characters or fewer. The previous value was kept.', 'alynt-enhanced-search' ),
                'error'
            );
            $custom_css = Alynt_ES_Search_Settings::get_settings()['custom_css'];
        }

        $sanitized['custom_css'] = $custom_css;

        return $sanitized;
    }

    private function sanitize_post_types($input) {
        if (!isset($input['post_types']) || !is_array($input['post_types'])) {
            add_settings_error(
                'alynt_es_settings',
                'alynt_es_post_types_missing',
                __('Choose at least one content type to include in search results.', 'alynt-enhanced-search'),
                'error'
            );

            return Alynt_ES_Search_Settings::get_settings()['post_types'];
        }

        $post_types = array_filter(array_map('sanitize_text_field', $input['post_types']));
        $allowed_post_types = array_keys(get_post_types(array('public' => true)));
        $post_types = array_values(array_intersect($post_types, $allowed_post_types));

        if (empty($post_types)) {
            add_settings_error(
                'alynt_es_settings',
                'alynt_es_post_types_empty',
                __('Choose at least one content type to include in search results.', 'alynt-enhanced-search'),
                'error'
            );

            return Alynt_ES_Search_Settings::get_settings()['post_types'];
        }

        return $post_types;
    }

    private function sanitize_int($input, $field, $default, $min, $max, $error_message) {
        if (!isset($input[$field]) || '' === $input[$field]) {
            return $default;
        }

        $value = intval($input[$field]);

        if ($value < $min || $value > $max) {
            add_settings_error('alynt_es_settings', 'alynt_es_' . $field . '_invalid', $error_message, 'error');
            return Alynt_ES_Search_Settings::get_settings()[$field];
        }

        return $value;
    }

    private function sanitize_color($input, $field, $default) {
        $value = isset($input[$field]) ? sanitize_text_field($input[$field]) : $default;

        if (preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) {
            return $value;
        }

        add_settings_error(
            'alynt_es_settings',
            'alynt_es_' . $field . '_invalid',
            sprintf(
                __('%s must be a six-digit hex color like #1a2b3c. The previous value was kept.', 'alynt-enhanced-search'),
                $this->get_field_label($field)
            ),
            'error'
        );

        return Alynt_ES_Search_Settings::get_settings()[$field];
    }

    private function get_color_defaults() {
        return array(
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
            'search_submit_text_hover_color' => '#ffffff'
        );
    }

    private function get_field_label($field) {
        $labels = array(
            'search_icon_color' => __('Search icon color', 'alynt-enhanced-search'),
            'search_page_bg_color' => __('Search page background color', 'alynt-enhanced-search'),
            'main_title_text_color' => __('Main title text color', 'alynt-enhanced-search'),
            'toggle_pill_bg_color' => __('Toggle pill background color', 'alynt-enhanced-search'),
            'toggle_pill_text_color' => __('Toggle pill text color', 'alynt-enhanced-search'),
            'toggle_pill_active_bg_color' => __('Active toggle pill background color', 'alynt-enhanced-search'),
            'toggle_pill_active_text_color' => __('Active toggle pill text color', 'alynt-enhanced-search'),
            'result_card_border_color' => __('Result card border color', 'alynt-enhanced-search'),
            'card_title_text_color' => __('Card title text color', 'alynt-enhanced-search'),
            'card_excerpt_container_bg_color' => __('Card excerpt container background color', 'alynt-enhanced-search'),
            'card_excerpt_text_color' => __('Card excerpt text color', 'alynt-enhanced-search'),
            'category_border_color' => __('Category border color', 'alynt-enhanced-search'),
            'category_text_color' => __('Category text color', 'alynt-enhanced-search'),
            'category_bg_color' => __('Category background color', 'alynt-enhanced-search'),
            'product_image_bg_color' => __('Product image background color', 'alynt-enhanced-search'),
            'result_card_bg_color' => __('Result card background color', 'alynt-enhanced-search'),
            'pagination_item_bg_color' => __('Pagination item background color', 'alynt-enhanced-search'),
            'pagination_item_text_color' => __('Pagination item text color', 'alynt-enhanced-search'),
            'pagination_current_bg_color' => __('Current pagination item background color', 'alynt-enhanced-search'),
            'pagination_current_text_color' => __('Current pagination item text color', 'alynt-enhanced-search'),
            'pagination_item_border_color' => __('Pagination item border color', 'alynt-enhanced-search'),
            'search_form_border_color' => __('Search form border color', 'alynt-enhanced-search'),
            'search_form_bg_color' => __('Search form background color', 'alynt-enhanced-search'),
            'search_submit_bg_color' => __('Search submit button background color', 'alynt-enhanced-search'),
            'search_submit_bg_hover_color' => __('Search submit button background color on hover', 'alynt-enhanced-search'),
            'search_submit_text_color' => __('Search submit button text color', 'alynt-enhanced-search'),
            'search_submit_text_hover_color' => __('Search submit button text color on hover', 'alynt-enhanced-search')
        );

        return isset($labels[$field]) ? $labels[$field] : $field;
    }
}
