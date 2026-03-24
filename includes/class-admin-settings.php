<?php
/**
 * Admin settings bootstrap for Alynt Enhanced Search.
 *
 * @package    Alynt_Enhanced_Search
 * @subpackage Alynt_Enhanced_Search/includes
 * @since      1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Bootstrap class for the plugin admin settings.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */
class Alynt_ES_Admin_Settings {

    private $page;

	/**
	 * Constructor. Initializes and wires the admin settings components.
	 *
	 * @since 1.0.0
	 *
	 * @param Alynt_ES_Admin_Settings_Page|null $page Admin settings page instance.
	 */
	public function __construct($page = null) {
        if ($page instanceof Alynt_ES_Admin_Settings_Page) {
            $this->page = $page;
            return;
        }

        $fields = new Alynt_ES_Admin_Settings_Fields();
        $sanitizer = new Alynt_ES_Admin_Settings_Sanitizer();
        $registry = new Alynt_ES_Admin_Settings_Registry($fields, $sanitizer);

        $this->page = new Alynt_ES_Admin_Settings_Page($registry);
    }
}
