<?php
/**
 * Plugin Name:       Alynt Enhanced Search
 * Plugin URI:        https://alynt.com
 * Description:       A minimalistic, AJAX-powered search plugin with grid layout and WooCommerce integration.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Alynt
 * Author URI:        https://alynt.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       alynt-enhanced-search
 * Domain Path:       /languages
 * GitHub Plugin URI: NichlasB/alynt-enhanced-search
 *
 * @package Alynt_Enhanced_Search
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ALYNT_ES_VERSION', '1.0.0' );
define( 'ALYNT_ES_PLUGIN_FILE', __FILE__ );
define( 'ALYNT_ES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ALYNT_ES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once ALYNT_ES_PLUGIN_DIR . 'includes/class-alynt-es-activator.php';
require_once ALYNT_ES_PLUGIN_DIR . 'includes/class-alynt-es-deactivator.php';
require_once ALYNT_ES_PLUGIN_DIR . 'includes/class-alynt-enhanced-search.php';

new Alynt_Enhanced_Search();
