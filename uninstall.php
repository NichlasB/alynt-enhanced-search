<?php
/**
 * Uninstall script for Alynt Enhanced Search
 * 
 * This file is executed when the plugin is uninstalled (deleted) from WordPress.
 * It removes all plugin data from the database to ensure a clean uninstall.
 */

// Prevent direct access
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Security check - make sure this is being called by WordPress uninstall process
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Remove all plugin options from the database
 */
function alynt_es_remove_plugin_options() {
    // Remove main plugin settings
    delete_option('alynt_es_settings');
    
    // Remove any potential site-wide options (for multisite compatibility)
    delete_site_option('alynt_es_settings');
}

/**
 * Remove all plugin transients (cache data)
 */
function alynt_es_remove_plugin_transients() {
    global $wpdb;
    
    // Delete all transients that start with 'alynt_es_'
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_alynt_es_%' OR option_name LIKE '_transient_timeout_alynt_es_%'");
    
    // For multisite installations, also clean up site transients
    if (is_multisite()) {
        $wpdb->query("DELETE FROM {$wpdb->sitemeta} WHERE meta_key LIKE '_site_transient_alynt_es_%' OR meta_key LIKE '_site_transient_timeout_alynt_es_%'");
    }
}

/**
 * Remove any custom database tables (if any were created)
 * Note: This plugin doesn't create custom tables, but this is here for completeness
 */
function alynt_es_remove_custom_tables() {
    // This plugin doesn't create custom tables, but if it did, they would be removed here
    // Example:
    // global $wpdb;
    // $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}alynt_es_custom_table");
}

/**
 * Remove any uploaded files or directories created by the plugin
 */
function alynt_es_remove_plugin_files() {
    // This plugin doesn't create additional files in wp-content/uploads,
    // but if it did, they would be removed here
    
    // Example for removing a custom upload directory:
    // $upload_dir = wp_upload_dir();
    // $plugin_dir = $upload_dir['basedir'] . '/alynt-enhanced-search/';
    // if (is_dir($plugin_dir)) {
    //     alynt_es_recursive_rmdir($plugin_dir);
    // }
}

/**
 * Remove any custom user meta data created by the plugin
 */
function alynt_es_remove_user_meta() {
    global $wpdb;
    
    // Remove any user meta data that might have been created by the plugin
    // This plugin doesn't store user-specific data, but this is here for completeness
    $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'alynt_es_%'");
}

/**
 * Remove any custom post meta data created by the plugin
 */
function alynt_es_remove_post_meta() {
    global $wpdb;
    
    // Remove any post meta data that might have been created by the plugin
    // This plugin doesn't store post-specific meta data, but this is here for completeness
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'alynt_es_%'");
}

/**
 * Clear any WordPress caches that might contain plugin data
 */
function alynt_es_clear_caches() {
    // Clear WordPress object cache
    wp_cache_flush();
    
    // Clear any opcode caches if available
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    
    // Flush rewrite rules to clean up any custom rules
    flush_rewrite_rules();
}

/**
 * Helper function to recursively remove directories
 * (Not used by this plugin, but included for completeness)
 */
function alynt_es_recursive_rmdir($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    
    $files = array_diff(scandir($dir), array('.', '..'));
    
    foreach ($files as $file) {
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($path)) {
            alynt_es_recursive_rmdir($path);
        } else {
            unlink($path);
        }
    }
    
    return rmdir($dir);
}

/**
 * Main uninstall function
 * Execute all cleanup functions
 */
function alynt_es_uninstall_plugin() {
    // Remove plugin options
    alynt_es_remove_plugin_options();
    
    // Remove plugin transients/cache
    alynt_es_remove_plugin_transients();
    
    // Remove custom tables (if any)
    alynt_es_remove_custom_tables();
    
    // Remove plugin files (if any)
    alynt_es_remove_plugin_files();
    
    // Remove user meta data (if any)
    alynt_es_remove_user_meta();
    
    // Remove post meta data (if any)
    alynt_es_remove_post_meta();
    
    // Clear caches
    alynt_es_clear_caches();
}

// Execute the uninstall process
alynt_es_uninstall_plugin();

// Log the uninstall for debugging purposes (optional)
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('Alynt Enhanced Search plugin has been completely uninstalled and all data removed.');
}
