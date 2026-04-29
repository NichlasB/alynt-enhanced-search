<?php
/**
 * Admin settings page for Alynt Enhanced Search.
 *
 * @package    Alynt_Enhanced_Search
 * @subpackage Alynt_Enhanced_Search/admin
 * @since      1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registers and renders the plugin's admin settings page.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */
class Alynt_ES_Admin_Settings_Page {

    private $registry;

	/**
	 * Constructor. Registers admin menu and settings initialization hooks.
	 *
	 * @since 1.0.0
	 *
	 * @param Alynt_ES_Admin_Settings_Registry $registry Settings registry instance.
	 */
	public function __construct($registry) {
        $this->registry = $registry;

        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this->registry, 'init_settings'));
    }

	/**
	 * Registers the plugin settings page in the WordPress admin menu.
	 *
	 * @since 1.0.0
	 *
	 * @return void
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
	 * Outputs the HTML for the plugin settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Alynt Enhanced Search Settings', 'alynt-enhanced-search'); ?></h1>
            <hr class="wp-header-end">

            <div class="alynt-es-admin-header">
                <p><?php esc_html_e('Configure your enhanced search functionality and appearance.', 'alynt-enhanced-search'); ?></p>

                <div class="alynt-es-shortcode-info">
                    <h2><?php esc_html_e('Shortcode Usage', 'alynt-enhanced-search'); ?></h2>
                    <p><?php esc_html_e('Use the following shortcodes to add search functionality anywhere on your site:', 'alynt-enhanced-search'); ?></p>
                    <code>[alynt_es_search]</code> - <?php esc_html_e('Default search button', 'alynt-enhanced-search'); ?><br>
                    <code>[alynt_es_search type="icon"]</code> - <?php esc_html_e('Search icon', 'alynt-enhanced-search'); ?><br>
                    <code>[alynt_es_search text="Find Content"]</code> - <?php esc_html_e('Custom button text', 'alynt-enhanced-search'); ?><br>
                    <code>[alynt_es_search class="my-custom-class"]</code> - <?php esc_html_e('Add custom CSS class', 'alynt-enhanced-search'); ?>
                </div>
            </div>

            <form method="post" action="options.php" class="alynt-es-settings-form">
                <?php
                settings_fields('alynt_es_settings_group');
                do_settings_sections('alynt-enhanced-search');
                submit_button(__('Save Settings', 'alynt-enhanced-search'));
                ?>
            </form>
        </div>
        <?php
    }
}
