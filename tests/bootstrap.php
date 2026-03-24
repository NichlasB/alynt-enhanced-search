<?php

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', dirname( __DIR__ ) . DIRECTORY_SEPARATOR );
}

if ( ! defined( 'PLUGIN_PATH' ) ) {
    define( 'PLUGIN_PATH', dirname( __DIR__ ) );
}

require_once PLUGIN_PATH . '/vendor/autoload.php';
require_once PLUGIN_PATH . '/includes/class-alynt-es-loader.php';

if ( ! function_exists( 'add_action' ) ) {
    function add_action() {}
}

if ( ! function_exists( 'register_activation_hook' ) ) {
    function register_activation_hook() {}
}

if ( ! function_exists( 'register_deactivation_hook' ) ) {
    function register_deactivation_hook() {}
}

if ( ! function_exists( 'plugin_dir_path' ) ) {
    function plugin_dir_path( $file ) {
        return trailingslashit( dirname( $file ) );
    }
}

if ( ! function_exists( 'plugin_dir_url' ) ) {
    function plugin_dir_url() {
        return 'http://example.com/wp-content/plugins/alynt-enhanced-search/';
    }
}

if ( ! function_exists( 'plugin_basename' ) ) {
    function plugin_basename( $file ) {
        return basename( dirname( $file ) ) . '/' . basename( $file );
    }
}

if ( ! function_exists( 'trailingslashit' ) ) {
    function trailingslashit( $value ) {
        return rtrim( $value, '/\\' ) . '/';
    }
}

require_once PLUGIN_PATH . '/alynt-enhanced-search.php';
