<?php
/**
 * Search template functionality for Alynt Enhanced Search
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Alynt_ES_Search_Template {
    
    public function __construct() {
        add_filter('template_include', array($this, 'load_search_template'));
        add_action('wp_head', array($this, 'add_cache_headers'));
        add_action('wp_head', array($this, 'add_dynamic_css'));
    }
    
    /**
     * Load custom search template
     */
    public function load_search_template($template) {
        if (is_search()) {
            $custom_template = ALYNT_ES_PLUGIN_DIR . 'templates/search.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        return $template;
    }
    
    /**
     * Add cache-busting headers for search pages
     */
    public function add_cache_headers() {
        if (is_search()) {
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
        }
    }
    
    /**
     * Add dynamic CSS based on color settings
     */
    public function add_dynamic_css() {
        if (is_search()) {
            $settings = self::get_settings();
            
            echo '<style type="text/css" id="alynt-es-dynamic-colors">';
            
            // Search icon color
            if (!empty($settings['search_icon_color'])) {
                echo '.ct-header .alynt-es-search-icon { color: ' . esc_attr($settings['search_icon_color']) . ' !important; }';
            }
            
            // Search page background
            if (!empty($settings['search_page_bg_color'])) {
                echo '.alynt-es-search-page { background-color: ' . esc_attr($settings['search_page_bg_color']) . ' !important; }';
            }
            
            // Main title text color
            if (!empty($settings['main_title_text_color'])) {
                echo '.alynt-es-main-title { color: ' . esc_attr($settings['main_title_text_color']) . ' !important; }';
            }
            
            // Toggle pill colors
            if (!empty($settings['toggle_pill_bg_color'])) {
                echo '.alynt-es-toggle-pill { background-color: ' . esc_attr($settings['toggle_pill_bg_color']) . ' !important; }';
            }
            if (!empty($settings['toggle_pill_text_color'])) {
                echo '.alynt-es-toggle-pill { color: ' . esc_attr($settings['toggle_pill_text_color']) . ' !important; }';
            }
            
            // Active toggle pill colors
            if (!empty($settings['toggle_pill_active_bg_color'])) {
                echo '.alynt-es-toggle-pill.active { background-color: ' . esc_attr($settings['toggle_pill_active_bg_color']) . ' !important; }';
            }
            if (!empty($settings['toggle_pill_active_text_color'])) {
                echo '.alynt-es-toggle-pill.active { color: ' . esc_attr($settings['toggle_pill_active_text_color']) . ' !important; }';
            }
            
            // Result card and image border colors
            if (!empty($settings['result_card_border_color'])) {
                echo '.alynt-es-result-card, .alynt-es-card-image { border-color: ' . esc_attr($settings['result_card_border_color']) . ' !important; }';
            }
            
            // Card title text color
            if (!empty($settings['card_title_text_color'])) {
                echo '.alynt-es-card-title { color: ' . esc_attr($settings['card_title_text_color']) . ' !important; }';
            }
            
            // Card excerpt container background color
            if (!empty($settings['card_excerpt_container_bg_color'])) {
                echo '.alynt-es-card-excerpt-container { background-color: ' . esc_attr($settings['card_excerpt_container_bg_color']) . ' !important; }';
            }
            
            // Card excerpt text color
            if (!empty($settings['card_excerpt_text_color'])) {
                echo '.alynt-es-card-excerpt { color: ' . esc_attr($settings['card_excerpt_text_color']) . ' !important; }';
            }
            
            // Category colors
            if (!empty($settings['category_border_color'])) {
                echo '.alynt-es-card-category { border-color: ' . esc_attr($settings['category_border_color']) . ' !important; }';
            }
            if (!empty($settings['category_text_color'])) {
                echo '.alynt-es-card-category { color: ' . esc_attr($settings['category_text_color']) . ' !important; }';
            }
            if (!empty($settings['category_bg_color'])) {
                echo '.alynt-es-card-category { background-color: ' . esc_attr($settings['category_bg_color']) . ' !important; }';
            }
            
            // Product image background color
            if (!empty($settings['product_image_bg_color'])) {
                echo '.alynt-es-card-image.product-image { background-color: ' . esc_attr($settings['product_image_bg_color']) . ' !important; }';
            }
            
            // Result card background color
            if (!empty($settings['result_card_bg_color'])) {
                echo '.alynt-es-result-card { background-color: ' . esc_attr($settings['result_card_bg_color']) . ' !important; }';
            }
            
            // Pagination item colors
            if (!empty($settings['pagination_item_bg_color'])) {
                echo '.alynt-es-pagination-item { background-color: ' . esc_attr($settings['pagination_item_bg_color']) . ' !important; }';
            }
            if (!empty($settings['pagination_item_text_color'])) {
                echo '.alynt-es-pagination-item { color: ' . esc_attr($settings['pagination_item_text_color']) . ' !important; }';
            }
            
            // Current pagination item colors
            if (!empty($settings['pagination_current_bg_color'])) {
                echo '.alynt-es-pagination-item.current { background-color: ' . esc_attr($settings['pagination_current_bg_color']) . ' !important; }';
            }
            if (!empty($settings['pagination_current_text_color'])) {
                echo '.alynt-es-pagination-item.current { color: ' . esc_attr($settings['pagination_current_text_color']) . ' !important; }';
            }
            
            // Pagination item border color
            if (!empty($settings['pagination_item_border_color'])) {
                echo '.alynt-es-pagination-item { border-color: ' . esc_attr($settings['pagination_item_border_color']) . ' !important; }';
            }
            
            // Search form colors
            if (!empty($settings['search_form_border_color'])) {
                echo '.alynt-es-search-form { border-color: ' . esc_attr($settings['search_form_border_color']) . ' !important; }';
            }
            if (!empty($settings['search_form_bg_color'])) {
                echo '.alynt-es-search-form { background-color: ' . esc_attr($settings['search_form_bg_color']) . ' !important; }';
            }
            
            // Search submit button colors
            if (!empty($settings['search_submit_bg_color'])) {
                echo '.alynt-es-search-submit { background-color: ' . esc_attr($settings['search_submit_bg_color']) . ' !important; }';
            }
            if (!empty($settings['search_submit_text_color'])) {
                echo '.alynt-es-search-submit { color: ' . esc_attr($settings['search_submit_text_color']) . ' !important; }';
            }
            
            // Search submit button hover colors
            if (!empty($settings['search_submit_bg_hover_color'])) {
                echo '.alynt-es-search-submit:hover { background-color: ' . esc_attr($settings['search_submit_bg_hover_color']) . ' !important; }';
            }
            if (!empty($settings['search_submit_text_hover_color'])) {
                echo '.alynt-es-search-submit:hover { color: ' . esc_attr($settings['search_submit_text_hover_color']) . ' !important; }';
            }
            
            echo '</style>';
        }
    }
    
    /**
     * Get plugin settings
     */
    public static function get_settings() {
        return get_option('alynt_es_settings', array(
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
        ));
    }
    
    /**
     * Check if WooCommerce is active and products are enabled
     */
    public static function is_woocommerce_enabled() {
        $settings = self::get_settings();
        return class_exists('WooCommerce') && in_array('product', $settings['post_types']);
    }
}
