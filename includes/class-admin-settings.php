<?php
/**
 * Admin settings page for Alynt Enhanced Search
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Alynt_ES_Admin_Settings {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'init_settings'));
    }
    
    /**
     * Add admin menu item
     */
    public function add_admin_menu() {
        add_options_page(
            __('Alynt Enhanced Search', 'alynt-enhanced-search'),
            __('Enhanced Search', 'alynt-enhanced-search'),
            'manage_options',
            'alynt-enhanced-search',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Initialize settings
     */
    public function init_settings() {
        register_setting('alynt_es_settings_group', 'alynt_es_settings', array($this, 'sanitize_settings'));
        
        // General Settings Section
        add_settings_section(
            'alynt_es_general_section',
            __('General Settings', 'alynt-enhanced-search'),
            array($this, 'general_section_callback'),
            'alynt-enhanced-search'
        );
        
        // Post Types Field
        add_settings_field(
            'post_types',
            __('Post Types to Include', 'alynt-enhanced-search'),
            array($this, 'post_types_callback'),
            'alynt-enhanced-search',
            'alynt_es_general_section'
        );
        
        // Results Per Page Field
        add_settings_field(
            'results_per_page',
            __('Results Per Page', 'alynt-enhanced-search'),
            array($this, 'results_per_page_callback'),
            'alynt-enhanced-search',
            'alynt_es_general_section'
        );
        
        // Max Columns Field
        add_settings_field(
            'max_columns',
            __('Maximum Columns', 'alynt-enhanced-search'),
            array($this, 'max_columns_callback'),
            'alynt-enhanced-search',
            'alynt_es_general_section'
        );
        
        // Display Settings Section
        add_settings_section(
            'alynt_es_display_section',
            __('Display Settings', 'alynt-enhanced-search'),
            array($this, 'display_section_callback'),
            'alynt-enhanced-search'
        );
        
        // Show Excerpt Field
        add_settings_field(
            'show_excerpt',
            __('Show Excerpts', 'alynt-enhanced-search'),
            array($this, 'show_excerpt_callback'),
            'alynt-enhanced-search',
            'alynt_es_display_section'
        );
        
        // Excerpt Length Field
        add_settings_field(
            'excerpt_length',
            __('Excerpt Length (words)', 'alynt-enhanced-search'),
            array($this, 'excerpt_length_callback'),
            'alynt-enhanced-search',
            'alynt_es_display_section'
        );
        
        // Featured Images General Field
        add_settings_field(
            'show_featured_images_general',
            __('Show Featured Images (General Pages)', 'alynt-enhanced-search'),
            array($this, 'show_featured_images_general_callback'),
            'alynt-enhanced-search',
            'alynt_es_display_section'
        );
        
        // Featured Images Products Field
        add_settings_field(
            'show_featured_images_products',
            __('Show Featured Images (Products)', 'alynt-enhanced-search'),
            array($this, 'show_featured_images_products_callback'),
            'alynt-enhanced-search',
            'alynt_es_display_section'
        );
        
        // Color Settings Section
        add_settings_section(
            'alynt_es_color_section',
            __('Color Settings', 'alynt-enhanced-search'),
            array($this, 'color_section_callback'),
            'alynt-enhanced-search'
        );
        
        // Search Icon Color
        add_settings_field(
            'search_icon_color',
            __('Search Icon Color', 'alynt-enhanced-search'),
            array($this, 'search_icon_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Search Page Background Color
        add_settings_field(
            'search_page_bg_color',
            __('Search Page Background Color', 'alynt-enhanced-search'),
            array($this, 'search_page_bg_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Main Title Text Color
        add_settings_field(
            'main_title_text_color',
            __('Main Title Text Color', 'alynt-enhanced-search'),
            array($this, 'main_title_text_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Toggle Pill Background Color
        add_settings_field(
            'toggle_pill_bg_color',
            __('Toggle Pill Background Color', 'alynt-enhanced-search'),
            array($this, 'toggle_pill_bg_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Toggle Pill Text Color
        add_settings_field(
            'toggle_pill_text_color',
            __('Toggle Pill Text Color', 'alynt-enhanced-search'),
            array($this, 'toggle_pill_text_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Active Toggle Pill Background Color
        add_settings_field(
            'toggle_pill_active_bg_color',
            __('Active Toggle Pill Background Color', 'alynt-enhanced-search'),
            array($this, 'toggle_pill_active_bg_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Active Toggle Pill Text Color
        add_settings_field(
            'toggle_pill_active_text_color',
            __('Active Toggle Pill Text Color', 'alynt-enhanced-search'),
            array($this, 'toggle_pill_active_text_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Result Card Border Color
        add_settings_field(
            'result_card_border_color',
            __('Result Card & Image Border Color', 'alynt-enhanced-search'),
            array($this, 'result_card_border_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Card Title Text Color
        add_settings_field(
            'card_title_text_color',
            __('Card Title Text Color', 'alynt-enhanced-search'),
            array($this, 'card_title_text_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Card Excerpt Container Background Color
        add_settings_field(
            'card_excerpt_container_bg_color',
            __('Card Excerpt Container Background Color', 'alynt-enhanced-search'),
            array($this, 'card_excerpt_container_bg_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Card Excerpt Text Color
        add_settings_field(
            'card_excerpt_text_color',
            __('Card Excerpt Text Color', 'alynt-enhanced-search'),
            array($this, 'card_excerpt_text_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Category Border Color
        add_settings_field(
            'category_border_color',
            __('Category Border Color', 'alynt-enhanced-search'),
            array($this, 'category_border_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Category Text Color
        add_settings_field(
            'category_text_color',
            __('Category Text Color', 'alynt-enhanced-search'),
            array($this, 'category_text_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Category Background Color
        add_settings_field(
            'category_bg_color',
            __('Category Background Color', 'alynt-enhanced-search'),
            array($this, 'category_bg_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Product Image Background Color
        add_settings_field(
            'product_image_bg_color',
            __('Product Image Background Color', 'alynt-enhanced-search'),
            array($this, 'product_image_bg_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Result Card Background Color
        add_settings_field(
            'result_card_bg_color',
            __('Result Card Background Color', 'alynt-enhanced-search'),
            array($this, 'result_card_bg_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Pagination Item Background Color
        add_settings_field(
            'pagination_item_bg_color',
            __('Pagination Item Background Color', 'alynt-enhanced-search'),
            array($this, 'pagination_item_bg_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Pagination Item Text Color
        add_settings_field(
            'pagination_item_text_color',
            __('Pagination Item Text Color', 'alynt-enhanced-search'),
            array($this, 'pagination_item_text_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Current Pagination Item Background Color
        add_settings_field(
            'pagination_current_bg_color',
            __('Current Pagination Item Background Color', 'alynt-enhanced-search'),
            array($this, 'pagination_current_bg_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Current Pagination Item Text Color
        add_settings_field(
            'pagination_current_text_color',
            __('Current Pagination Item Text Color', 'alynt-enhanced-search'),
            array($this, 'pagination_current_text_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Pagination Item Border Color
        add_settings_field(
            'pagination_item_border_color',
            __('Pagination Item Border Color', 'alynt-enhanced-search'),
            array($this, 'pagination_item_border_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Search Form Border Color
        add_settings_field(
            'search_form_border_color',
            __('Search Form Border Color', 'alynt-enhanced-search'),
            array($this, 'search_form_border_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Search Form Background Color
        add_settings_field(
            'search_form_bg_color',
            __('Search Form Background Color', 'alynt-enhanced-search'),
            array($this, 'search_form_bg_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Search Submit Button Background Color
        add_settings_field(
            'search_submit_bg_color',
            __('Search Submit Button Background Color', 'alynt-enhanced-search'),
            array($this, 'search_submit_bg_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Search Submit Button Background Color (Hover)
        add_settings_field(
            'search_submit_bg_hover_color',
            __('Search Submit Button Background Color (Hover)', 'alynt-enhanced-search'),
            array($this, 'search_submit_bg_hover_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Search Submit Button Text Color
        add_settings_field(
            'search_submit_text_color',
            __('Search Submit Button Text Color', 'alynt-enhanced-search'),
            array($this, 'search_submit_text_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Search Submit Button Text Color (Hover)
        add_settings_field(
            'search_submit_text_hover_color',
            __('Search Submit Button Text Color (Hover)', 'alynt-enhanced-search'),
            array($this, 'search_submit_text_hover_color_callback'),
            'alynt-enhanced-search',
            'alynt_es_color_section'
        );
        
        // Custom CSS Section
        add_settings_section(
            'alynt_es_css_section',
            __('Custom CSS', 'alynt-enhanced-search'),
            array($this, 'css_section_callback'),
            'alynt-enhanced-search'
        );
        
        // Custom CSS Field
        add_settings_field(
            'custom_css',
            __('Custom CSS Styles', 'alynt-enhanced-search'),
            array($this, 'custom_css_callback'),
            'alynt-enhanced-search',
            'alynt_es_css_section'
        );
    }
    
    /**
     * Render the settings page
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Alynt Enhanced Search Settings', 'alynt-enhanced-search'); ?></h1>
            
            <div class="alynt-es-admin-header">
                <p><?php esc_html_e('Configure your enhanced search functionality and appearance.', 'alynt-enhanced-search'); ?></p>
                
                <div class="alynt-es-shortcode-info">
                    <h3><?php esc_html_e('Shortcode Usage', 'alynt-enhanced-search'); ?></h3>
                    <p><?php esc_html_e('Use the following shortcodes to add search functionality anywhere on your site:', 'alynt-enhanced-search'); ?></p>
                    <code>[alynt_es_search]</code> - <?php esc_html_e('Default search button', 'alynt-enhanced-search'); ?><br>
                    <code>[alynt_es_search type="icon"]</code> - <?php esc_html_e('Search icon', 'alynt-enhanced-search'); ?><br>
                    <code>[alynt_es_search text="Find Content"]</code> - <?php esc_html_e('Custom button text', 'alynt-enhanced-search'); ?><br>
                    <code>[alynt_es_search class="my-custom-class"]</code> - <?php esc_html_e('Add custom CSS class', 'alynt-enhanced-search'); ?>
                </div>
            </div>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('alynt_es_settings_group');
                do_settings_sections('alynt-enhanced-search');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Section callbacks
     */
    public function general_section_callback() {
        echo '<p>' . esc_html__('Configure which content types to include in search results and pagination settings.', 'alynt-enhanced-search') . '</p>';
    }
    
    public function display_section_callback() {
        echo '<p>' . esc_html__('Control how search results are displayed to your visitors.', 'alynt-enhanced-search') . '</p>';
    }
    
    public function color_section_callback() {
        echo '<p>' . esc_html__('Customize the colors of your search page elements. Use hex color codes (e.g., #ffffff for white).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function css_section_callback() {
        echo '<p>' . esc_html__('Add custom CSS to style the search page and results to match your theme.', 'alynt-enhanced-search') . '</p>';
    }
    
    /**
     * Field callbacks
     */
    public function post_types_callback() {
        $settings = get_option('alynt_es_settings');
        $selected_types = isset($settings['post_types']) ? $settings['post_types'] : array('post', 'page', 'product');
        
        $post_types = get_post_types(array('public' => true), 'objects');
        
        echo '<fieldset>';
        foreach ($post_types as $post_type) {
            $checked = in_array($post_type->name, $selected_types) ? 'checked' : '';
            echo '<label>';
            echo '<input type="checkbox" name="alynt_es_settings[post_types][]" value="' . esc_attr($post_type->name) . '" ' . $checked . '> ';
            echo esc_html($post_type->label);
            echo '</label><br>';
        }
        echo '</fieldset>';
        echo '<p class="description">' . esc_html__('Select which post types should be included in search results. Pages, Posts, and Products are selected by default.', 'alynt-enhanced-search') . '</p>';
    }
    
    public function results_per_page_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['results_per_page']) ? $settings['results_per_page'] : 12;
        
        echo '<input type="number" name="alynt_es_settings[results_per_page]" value="' . esc_attr($value) . '" min="1" max="50" class="small-text">';
        echo '<p class="description">' . esc_html__('Number of search results to display per page.', 'alynt-enhanced-search') . '</p>';
    }
    
    public function max_columns_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['max_columns']) ? $settings['max_columns'] : 3;
        
        echo '<select name="alynt_es_settings[max_columns]">';
        for ($i = 1; $i <= 5; $i++) {
            $selected = ($value == $i) ? 'selected' : '';
            echo '<option value="' . $i . '" ' . $selected . '>' . $i . ' ' . esc_html__('Column(s)', 'alynt-enhanced-search') . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . esc_html__('Maximum number of columns in the results grid (1-5).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function show_excerpt_callback() {
        $settings = get_option('alynt_es_settings');
        $checked = isset($settings['show_excerpt']) && $settings['show_excerpt'] ? 'checked' : '';
        
        echo '<label>';
        echo '<input type="checkbox" name="alynt_es_settings[show_excerpt]" value="1" ' . $checked . '> ';
        echo esc_html__('Display excerpts in search result cards', 'alynt-enhanced-search');
        echo '</label>';
    }
    
    public function excerpt_length_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['excerpt_length']) ? $settings['excerpt_length'] : 20;
        
        echo '<input type="number" name="alynt_es_settings[excerpt_length]" value="' . esc_attr($value) . '" min="5" max="100" class="small-text">';
        echo '<p class="description">' . esc_html__('Maximum number of words to display in excerpts.', 'alynt-enhanced-search') . '</p>';
    }
    
    public function show_featured_images_general_callback() {
        $settings = get_option('alynt_es_settings');
        $checked = isset($settings['show_featured_images_general']) && $settings['show_featured_images_general'] ? 'checked' : '';
        
        echo '<label>';
        echo '<input type="checkbox" name="alynt_es_settings[show_featured_images_general]" value="1" ' . $checked . '> ';
        echo esc_html__('Show featured images for general pages (posts, pages, etc.)', 'alynt-enhanced-search');
        echo '</label>';
    }
    
    public function show_featured_images_products_callback() {
        $settings = get_option('alynt_es_settings');
        $checked = isset($settings['show_featured_images_products']) && $settings['show_featured_images_products'] ? 'checked' : '';
        
        echo '<label>';
        echo '<input type="checkbox" name="alynt_es_settings[show_featured_images_products]" value="1" ' . $checked . '> ';
        echo esc_html__('Show featured images for WooCommerce products', 'alynt-enhanced-search');
        echo '</label>';
    }
    
    public function search_icon_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['search_icon_color']) ? $settings['search_icon_color'] : '#333333';
        
        echo '<input type="text" name="alynt_es_settings[search_icon_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#333333">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[search_icon_color]">';
        echo '<p class="description">' . esc_html__('Color for the search icon.', 'alynt-enhanced-search') . '</p>';
    }
    
    public function search_page_bg_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['search_page_bg_color']) ? $settings['search_page_bg_color'] : '#ffffff';
        
        echo '<input type="text" name="alynt_es_settings[search_page_bg_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#ffffff">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[search_page_bg_color]">';
        echo '<p class="description">' . esc_html__('Background color for the search page container (.alynt-es-search-page).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function main_title_text_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['main_title_text_color']) ? $settings['main_title_text_color'] : '#333333';
        
        echo '<input type="text" name="alynt_es_settings[main_title_text_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#333333">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[main_title_text_color]">';
        echo '<p class="description">' . esc_html__('Text color for the main search title (.alynt-es-main-title).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function toggle_pill_bg_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['toggle_pill_bg_color']) ? $settings['toggle_pill_bg_color'] : '#f0f0f0';
        
        echo '<input type="text" name="alynt_es_settings[toggle_pill_bg_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#f0f0f0">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[toggle_pill_bg_color]">';
        echo '<p class="description">' . esc_html__('Background color for toggle pills (.alynt-es-toggle-pill).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function toggle_pill_text_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['toggle_pill_text_color']) ? $settings['toggle_pill_text_color'] : '#666666';
        
        echo '<input type="text" name="alynt_es_settings[toggle_pill_text_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#666666">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[toggle_pill_text_color]">';
        echo '<p class="description">' . esc_html__('Text color for toggle pills (.alynt-es-toggle-pill).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function toggle_pill_active_bg_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['toggle_pill_active_bg_color']) ? $settings['toggle_pill_active_bg_color'] : '#007cba';
        
        echo '<input type="text" name="alynt_es_settings[toggle_pill_active_bg_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#007cba">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[toggle_pill_active_bg_color]">';
        echo '<p class="description">' . esc_html__('Background color for active toggle pills (.alynt-es-toggle-pill.active).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function toggle_pill_active_text_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['toggle_pill_active_text_color']) ? $settings['toggle_pill_active_text_color'] : '#ffffff';
        
        echo '<input type="text" name="alynt_es_settings[toggle_pill_active_text_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#ffffff">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[toggle_pill_active_text_color]">';
        echo '<p class="description">' . esc_html__('Text color for active toggle pills (.alynt-es-toggle-pill.active).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function result_card_border_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['result_card_border_color']) ? $settings['result_card_border_color'] : '#e0e0e0';
        
        echo '<input type="text" name="alynt_es_settings[result_card_border_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#e0e0e0">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[result_card_border_color]">';
        echo '<p class="description">' . esc_html__('Border color for result cards and card images (.alynt-es-result-card, .alynt-es-card-image).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function card_title_text_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['card_title_text_color']) ? $settings['card_title_text_color'] : '#333333';
        
        echo '<input type="text" name="alynt_es_settings[card_title_text_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#333333">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[card_title_text_color]">';
        echo '<p class="description">' . esc_html__('Text color for card titles (.alynt-es-card-title).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function card_excerpt_container_bg_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['card_excerpt_container_bg_color']) ? $settings['card_excerpt_container_bg_color'] : '#f9f9f9';
        
        echo '<input type="text" name="alynt_es_settings[card_excerpt_container_bg_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#f9f9f9">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[card_excerpt_container_bg_color]">';
        echo '<p class="description">' . esc_html__('Background color for card excerpt container (.alynt-es-card-excerpt-container).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function card_excerpt_text_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['card_excerpt_text_color']) ? $settings['card_excerpt_text_color'] : '#666666';
        
        echo '<input type="text" name="alynt_es_settings[card_excerpt_text_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#666666">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[card_excerpt_text_color]">';
        echo '<p class="description">' . esc_html__('Text color for card excerpts (.alynt-es-card-excerpt).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function category_border_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['category_border_color']) ? $settings['category_border_color'] : '#cccccc';
        
        echo '<input type="text" name="alynt_es_settings[category_border_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#cccccc">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[category_border_color]">';
        echo '<p class="description">' . esc_html__('Border color for category tags (.alynt-es-card-category).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function category_text_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['category_text_color']) ? $settings['category_text_color'] : '#666666';
        
        echo '<input type="text" name="alynt_es_settings[category_text_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#666666">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[category_text_color]">';
        echo '<p class="description">' . esc_html__('Text color for category tags (.alynt-es-card-category).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function category_bg_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['category_bg_color']) ? $settings['category_bg_color'] : '#f9f9f9';
        
        echo '<input type="text" name="alynt_es_settings[category_bg_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#f9f9f9">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[category_bg_color]">';
        echo '<p class="description">' . esc_html__('Background color for category tags (.alynt-es-card-category).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function product_image_bg_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['product_image_bg_color']) ? $settings['product_image_bg_color'] : '#ffffff';
        
        echo '<input type="text" name="alynt_es_settings[product_image_bg_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#ffffff">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[product_image_bg_color]">';
        echo '<p class="description">' . esc_html__('Background color for product images (.alynt-es-card-image.product-image).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function result_card_bg_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['result_card_bg_color']) ? $settings['result_card_bg_color'] : '#ffffff';
        
        echo '<input type="text" name="alynt_es_settings[result_card_bg_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#ffffff">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[result_card_bg_color]">';
        echo '<p class="description">' . esc_html__('Background color for result cards (.alynt-es-result-card).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function pagination_item_bg_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['pagination_item_bg_color']) ? $settings['pagination_item_bg_color'] : '#f0f0f0';
        
        echo '<input type="text" name="alynt_es_settings[pagination_item_bg_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#f0f0f0">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[pagination_item_bg_color]">';
        echo '<p class="description">' . esc_html__('Background color for pagination items (.alynt-es-pagination-item).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function pagination_item_text_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['pagination_item_text_color']) ? $settings['pagination_item_text_color'] : '#333333';
        
        echo '<input type="text" name="alynt_es_settings[pagination_item_text_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#333333">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[pagination_item_text_color]">';
        echo '<p class="description">' . esc_html__('Text color for pagination items (.alynt-es-pagination-item).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function pagination_current_bg_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['pagination_current_bg_color']) ? $settings['pagination_current_bg_color'] : '#007cba';
        
        echo '<input type="text" name="alynt_es_settings[pagination_current_bg_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#007cba">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[pagination_current_bg_color]">';
        echo '<p class="description">' . esc_html__('Background color for current pagination item (.alynt-es-pagination-item.current).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function pagination_current_text_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['pagination_current_text_color']) ? $settings['pagination_current_text_color'] : '#ffffff';
        
        echo '<input type="text" name="alynt_es_settings[pagination_current_text_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#ffffff">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[pagination_current_text_color]">';
        echo '<p class="description">' . esc_html__('Text color for current pagination item (.alynt-es-pagination-item.current).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function pagination_item_border_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['pagination_item_border_color']) ? $settings['pagination_item_border_color'] : '#e0e0e0';
        
        echo '<input type="text" name="alynt_es_settings[pagination_item_border_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#e0e0e0">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[pagination_item_border_color]">';
        echo '<p class="description">' . esc_html__('Border color for pagination items (.alynt-es-pagination-item).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function search_form_border_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['search_form_border_color']) ? $settings['search_form_border_color'] : '#e0e0e0';
        
        echo '<input type="text" name="alynt_es_settings[search_form_border_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#e0e0e0">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[search_form_border_color]">';
        echo '<p class="description">' . esc_html__('Border color for search form (.alynt-es-search-form).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function search_form_bg_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['search_form_bg_color']) ? $settings['search_form_bg_color'] : '#ffffff';
        
        echo '<input type="text" name="alynt_es_settings[search_form_bg_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#ffffff">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[search_form_bg_color]">';
        echo '<p class="description">' . esc_html__('Background color for search form (.alynt-es-search-form).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function search_submit_bg_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['search_submit_bg_color']) ? $settings['search_submit_bg_color'] : '#007cba';
        
        echo '<input type="text" name="alynt_es_settings[search_submit_bg_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#007cba">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[search_submit_bg_color]">';
        echo '<p class="description">' . esc_html__('Background color for search submit button (.alynt-es-search-submit).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function search_submit_bg_hover_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['search_submit_bg_hover_color']) ? $settings['search_submit_bg_hover_color'] : '#005a87';
        
        echo '<input type="text" name="alynt_es_settings[search_submit_bg_hover_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#005a87">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[search_submit_bg_hover_color]">';
        echo '<p class="description">' . esc_html__('Background color for search submit button on hover (.alynt-es-search-submit:hover).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function search_submit_text_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['search_submit_text_color']) ? $settings['search_submit_text_color'] : '#ffffff';
        
        echo '<input type="text" name="alynt_es_settings[search_submit_text_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#ffffff">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[search_submit_text_color]">';
        echo '<p class="description">' . esc_html__('Text color for search submit button (.alynt-es-search-submit).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function search_submit_text_hover_color_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['search_submit_text_hover_color']) ? $settings['search_submit_text_hover_color'] : '#ffffff';
        
        echo '<input type="text" name="alynt_es_settings[search_submit_text_hover_color]" value="' . esc_attr($value) . '" class="color-text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#ffffff">';
        echo '<input type="color" value="' . esc_attr($value) . '" class="color-picker" data-target="alynt_es_settings[search_submit_text_hover_color]">';
        echo '<p class="description">' . esc_html__('Text color for search submit button on hover (.alynt-es-search-submit:hover).', 'alynt-enhanced-search') . '</p>';
    }
    
    public function custom_css_callback() {
        $settings = get_option('alynt_es_settings');
        $value = isset($settings['custom_css']) ? $settings['custom_css'] : '';
        
        echo '<textarea name="alynt_es_settings[custom_css]" rows="10" cols="80" class="large-text code">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">' . esc_html__('Add custom CSS to style the search page and results. This CSS will be loaded on the search page only.', 'alynt-enhanced-search') . '</p>';
    }
    
    /**
     * Sanitize settings
     */
    public function sanitize_settings($input) {
        $sanitized = array();
        
        // Post types
        if (isset($input['post_types']) && is_array($input['post_types'])) {
            $sanitized['post_types'] = array_map('sanitize_text_field', $input['post_types']);
        } else {
            $sanitized['post_types'] = array();
        }
        
        // Results per page
        $sanitized['results_per_page'] = isset($input['results_per_page']) ? intval($input['results_per_page']) : 12;
        if ($sanitized['results_per_page'] < 1) $sanitized['results_per_page'] = 12;
        if ($sanitized['results_per_page'] > 50) $sanitized['results_per_page'] = 50;
        
        // Max columns
        $sanitized['max_columns'] = isset($input['max_columns']) ? intval($input['max_columns']) : 3;
        if ($sanitized['max_columns'] < 1) $sanitized['max_columns'] = 1;
        if ($sanitized['max_columns'] > 5) $sanitized['max_columns'] = 5;
        
        // Boolean fields
        $sanitized['show_excerpt'] = isset($input['show_excerpt']) ? true : false;
        $sanitized['show_featured_images_general'] = isset($input['show_featured_images_general']) ? true : false;
        $sanitized['show_featured_images_products'] = isset($input['show_featured_images_products']) ? true : false;
        
        // Excerpt length
        $sanitized['excerpt_length'] = isset($input['excerpt_length']) ? intval($input['excerpt_length']) : 20;
        if ($sanitized['excerpt_length'] < 5) $sanitized['excerpt_length'] = 5;
        if ($sanitized['excerpt_length'] > 100) $sanitized['excerpt_length'] = 100;
        
        // Color settings
        $color_fields = array(
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
        
        foreach ($color_fields as $field => $default) {
            $color_value = isset($input[$field]) ? sanitize_text_field($input[$field]) : $default;
            // Validate hex color format
            if (preg_match('/^#[0-9A-Fa-f]{6}$/', $color_value)) {
                $sanitized[$field] = $color_value;
            } else {
                $sanitized[$field] = $default;
            }
        }
        
        // Custom CSS
        $sanitized['custom_css'] = isset($input['custom_css']) ? wp_strip_all_tags($input['custom_css']) : '';
        
        return $sanitized;
    }
}
