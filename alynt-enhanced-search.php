<?php
/**
 * Plugin Name: Alynt Enhanced Search
 * Plugin URI: https://alynt.com
 * Description: A minimalistic, AJAX-powered search plugin with grid layout and WooCommerce integration.
 * Version: 1.0.0
 * Author: Alynt
 * License: GPL v2 or later
 * Text Domain: alynt-enhanced-search
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ALYNT_ES_VERSION', '1.0.0');
define('ALYNT_ES_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ALYNT_ES_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ALYNT_ES_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Main plugin class
class Alynt_Enhanced_Search {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        // Load text domain for translations
        load_plugin_textdomain('alynt-enhanced-search', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Initialize plugin components
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    private function load_dependencies() {
        require_once ALYNT_ES_PLUGIN_DIR . 'includes/class-shortcode.php';
        require_once ALYNT_ES_PLUGIN_DIR . 'includes/class-search-template.php';
        require_once ALYNT_ES_PLUGIN_DIR . 'includes/class-ajax-handler.php';
        require_once ALYNT_ES_PLUGIN_DIR . 'includes/class-admin-settings.php';
    }
    
    private function init_hooks() {
        // Initialize components
        new Alynt_ES_Shortcode();
        new Alynt_ES_Search_Template();
        new Alynt_ES_Ajax_Handler();
        
        if (is_admin()) {
            new Alynt_ES_Admin_Settings();
        }
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    public function enqueue_scripts() {
        wp_enqueue_style('alynt-es-style', ALYNT_ES_PLUGIN_URL . 'assets/css/style.css', array(), ALYNT_ES_VERSION);
        wp_enqueue_script('alynt-es-script', ALYNT_ES_PLUGIN_URL . 'assets/js/script.js', array('jquery'), ALYNT_ES_VERSION, true);
        
        // Localize script for AJAX
        wp_localize_script('alynt-es-script', 'alynt_es_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('alynt_es_nonce'),
            'search_delay' => 300
        ));
    }
    
    public function enqueue_admin_scripts($hook) {
        if ('settings_page_alynt-enhanced-search' !== $hook) {
            return;
        }
        
        wp_enqueue_style('alynt-es-admin-style', ALYNT_ES_PLUGIN_URL . 'assets/css/admin-style.css', array(), ALYNT_ES_VERSION);
        wp_enqueue_script('alynt-es-admin-script', ALYNT_ES_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery'), ALYNT_ES_VERSION, true);
    }
    
    public function activate() {
        // Set default options
        $default_options = array(
            'post_types' => array('post', 'page', 'product'),
            'results_per_page' => 12,
            'show_excerpt' => true,
            'excerpt_length' => 20,
            'max_columns' => 3,
            'show_featured_images_general' => true,
            'show_featured_images_products' => true,
            'custom_css' => ''
        );
        
        add_option('alynt_es_settings', $default_options);
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        // Clear all search cache
        $this->clear_search_cache();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Clear all search transient cache
     */
    public function clear_search_cache() {
        global $wpdb;
        
        // Delete all transients that start with 'alynt_es_'
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_alynt_es_%' OR option_name LIKE '_transient_timeout_alynt_es_%'");
    }
}

// Initialize the plugin
new Alynt_Enhanced_Search();
