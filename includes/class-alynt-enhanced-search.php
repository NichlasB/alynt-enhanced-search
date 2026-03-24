<?php
/**
 * Main plugin class for Alynt Enhanced Search.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bootstraps and initializes all plugin components.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */
class Alynt_Enhanced_Search {

	/**
	 * Constructor. Registers activation, deactivation hooks and initializes the plugin on init.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'init' ) );
		register_activation_hook( ALYNT_ES_PLUGIN_FILE, array( 'Alynt_ES_Activator', 'activate' ) );
		register_deactivation_hook( ALYNT_ES_PLUGIN_FILE, array( 'Alynt_ES_Deactivator', 'deactivate' ) );
	}

	/**
	 * Loads dependencies and registers hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {
		$this->load_dependencies();
		$this->init_hooks();
	}

	/**
	 * Loads the plugin text domain.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'alynt-enhanced-search', false, dirname( plugin_basename( ALYNT_ES_PLUGIN_FILE ) ) . '/languages/' );
	}

	/**
	 * Requires all plugin class files.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function load_dependencies() {
		require_once ALYNT_ES_PLUGIN_DIR . 'includes/class-alynt-es-loader.php';
		Alynt_ES_Loader::load();
	}

	/**
	 * Instantiates feature classes and registers their WordPress hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function init_hooks() {
		new Alynt_ES_Shortcode();
		new Alynt_ES_Search_Template();
		new Alynt_ES_Ajax_Handler();

		if ( is_admin() ) {
			new Alynt_ES_Admin_Settings();
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Enqueues front-end CSS and JavaScript assets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( ! $this->should_enqueue_search_shell_assets() ) {
			return;
		}

		wp_enqueue_style( 'alynt-es-search-shell', ALYNT_ES_PLUGIN_URL . 'assets/css/search-shell.css', array(), ALYNT_ES_VERSION );

		if ( ! is_search() ) {
			return;
		}

		$settings = Alynt_ES_Search_Settings::get_settings();

		wp_enqueue_style( 'alynt-es-search-controls', ALYNT_ES_PLUGIN_URL . 'assets/css/search-controls.css', array( 'alynt-es-search-shell' ), ALYNT_ES_VERSION );
		wp_enqueue_style( 'alynt-es-search-results', ALYNT_ES_PLUGIN_URL . 'assets/css/search-results.css', array( 'alynt-es-search-controls' ), ALYNT_ES_VERSION );
		wp_enqueue_style( 'alynt-es-search-responsive', ALYNT_ES_PLUGIN_URL . 'assets/css/search-responsive.css', array( 'alynt-es-search-results' ), ALYNT_ES_VERSION );

		wp_enqueue_script( 'alynt-es-search-utils', ALYNT_ES_PLUGIN_URL . 'assets/js/search-utils.js', array( 'jquery' ), ALYNT_ES_VERSION, true );
		wp_enqueue_script( 'alynt-es-search-render', ALYNT_ES_PLUGIN_URL . 'assets/js/search-render.js', array( 'jquery', 'alynt-es-search-utils' ), ALYNT_ES_VERSION, true );
		wp_enqueue_script( 'alynt-es-search-api', ALYNT_ES_PLUGIN_URL . 'assets/js/search-api.js', array( 'jquery', 'alynt-es-search-render' ), ALYNT_ES_VERSION, true );
		wp_enqueue_script( 'alynt-es-search-events', ALYNT_ES_PLUGIN_URL . 'assets/js/search-events.js', array( 'jquery', 'alynt-es-search-api' ), ALYNT_ES_VERSION, true );
		wp_enqueue_script( 'alynt-es-search-init', ALYNT_ES_PLUGIN_URL . 'assets/js/search-init.js', array( 'jquery', 'alynt-es-search-events' ), ALYNT_ES_VERSION, true );

		wp_localize_script(
			'alynt-es-search-init',
			'alyntESConfig',
			array(
				'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
				'nonce'              => wp_create_nonce( 'alynt_es_nonce' ),
				'searchDelay'        => 300,
				'requestTimeout'     => 10000,
				'woocommerceEnabled' => Alynt_ES_Search_Settings::is_woocommerce_enabled(),
				'showExcerpt'        => $settings['show_excerpt'],
				'initialQuery'       => get_search_query(),
				'i18n'               => array(
					'searchFailed'         => esc_html__( 'We could not complete your search. Please try again.', 'alynt-enhanced-search' ),
					'serverError'          => esc_html__( 'Search is temporarily unavailable. Please try again in a moment.', 'alynt-enhanced-search' ),
					'networkError'         => esc_html__( 'You appear to be offline. Check your connection and try again.', 'alynt-enhanced-search' ),
					'timeoutError'         => esc_html__( 'Search is taking longer than expected. Please try again.', 'alynt-enhanced-search' ),
					'sessionExpired'       => esc_html__( 'Your session expired. Refresh the page and try your search again.', 'alynt-enhanced-search' ),
					'retrySearch'          => esc_html__( 'Try again', 'alynt-enhanced-search' ),
					'searching'            => esc_html__( 'Searching...', 'alynt-enhanced-search' ),
					'submitSearch'         => esc_html__( 'Submit search', 'alynt-enhanced-search' ),
					/* translators: %s: search query. */
					'noResults'            => esc_html__( 'No results found for "%s". Try different keywords.', 'alynt-enhanced-search' ),
					'noQuery'              => esc_html__( 'Enter a search term to find content.', 'alynt-enhanced-search' ),
					'oneResultFound'       => esc_html__( '1 result found', 'alynt-enhanced-search' ),
					/* translators: %d: number of results. */
					'multipleResultsFound' => esc_html__( '%d results found', 'alynt-enhanced-search' ),
				),
			)
		);
	}

	/**
	 * Enqueues admin CSS and JavaScript assets on the plugin's settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook The current admin page hook suffix.
	 *
	 * @return void
	 */
	public function enqueue_admin_scripts( $hook ) {
		if ( 'settings_page_alynt-enhanced-search' !== $hook ) {
			return;
		}

		wp_enqueue_style( 'alynt-es-admin-style', ALYNT_ES_PLUGIN_URL . 'assets/css/admin-style.css', array(), ALYNT_ES_VERSION );
		wp_enqueue_script( 'alynt-es-admin-script', ALYNT_ES_PLUGIN_URL . 'assets/js/admin-script.js', array( 'jquery' ), ALYNT_ES_VERSION, true );

		wp_localize_script(
			'alynt-es-admin-script',
			'alyntESAdminConfig',
			array(
				'i18n' => array(
					'invalidColor'   => esc_html__( 'Please enter a six-digit hex color like #1a2b3c.', 'alynt-enhanced-search' ),
					'unsavedChanges' => esc_html__( 'You have unsaved changes. Leave this page without saving?', 'alynt-enhanced-search' ),
				),
			)
		);
	}

	/**
	 * Plugin activation callback. Adds default settings option and flushes rewrite rules.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function activate() {
		Alynt_ES_Activator::activate();
	}

	/**
	 * Plugin deactivation callback. Clears the search cache and flushes rewrite rules.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function deactivate() {
		Alynt_ES_Deactivator::deactivate();
	}

	/**
	 * Clears all search transient cache entries.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function clear_search_cache() {
		Alynt_ES_Search_Cache_Manager::clear_transients();
	}

	/**
	 * Determines whether the shell stylesheet should be enqueued.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function should_enqueue_search_shell_assets() {
		if ( is_search() ) {
			return true;
		}

		if ( ! is_singular() ) {
			return false;
		}

		$post = get_post();

		if ( ! $post instanceof WP_Post ) {
			return false;
		}

		return has_shortcode( $post->post_content, 'alynt_es_search' );
	}
}
