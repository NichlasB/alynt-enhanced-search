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

		Alynt_ES_Loader::load_shared();

		if ( wp_doing_ajax() ) {
			Alynt_ES_Loader::load_ajax();
			return;
		}

		if ( is_admin() ) {
			Alynt_ES_Loader::load_admin();
			return;
		}

		Alynt_ES_Loader::load_frontend();
	}

	/**
	 * Instantiates feature classes and registers their WordPress hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function init_hooks() {
		if ( wp_doing_ajax() ) {
			new Alynt_ES_Ajax_Handler();
			return;
		}

		if ( is_admin() ) {
			new Alynt_ES_Admin_Settings();
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
			return;
		}

		new Alynt_ES_Shortcode();
		new Alynt_ES_Search_Template();

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueues front-end CSS and JavaScript assets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( ! is_search() ) {
			$this->enqueue_search_shell_styles();
			return;
		}

		$settings = Alynt_ES_Search_Settings::get_settings();

		if ( $this->asset_exists( 'assets/dist/frontend/index.css' ) ) {
			wp_enqueue_style( 'alynt-es-search-shell', $this->get_asset_url( 'assets/dist/frontend/index.css' ), array(), $this->get_asset_version( 'assets/dist/frontend/index.css' ) );
		} else {
			$this->enqueue_search_source_styles();
		}

		if ( $this->asset_exists( 'assets/dist/frontend/index.js' ) ) {
			wp_enqueue_script( 'alynt-es-search-init', $this->get_asset_url( 'assets/dist/frontend/index.js' ), array( 'jquery' ), $this->get_asset_version( 'assets/dist/frontend/index.js' ), true );
		} else {
			$this->enqueue_search_source_scripts();
		}

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

		$code_editor_settings = wp_enqueue_code_editor(
			array(
				'type'       => 'text/css',
				'codemirror' => array(
					'indentUnit'     => 4,
					'tabSize'        => 4,
					'indentWithTabs' => true,
					'lineNumbers'    => true,
					'lineWrapping'   => true,
					'extraKeys'      => array(
						'Tab'       => 'defaultTab',
						'Shift-Tab' => 'indentLess',
					),
				),
			)
		);

		if ( $this->asset_exists( 'assets/dist/admin/index.css' ) ) {
			wp_enqueue_style( 'alynt-es-admin-style', $this->get_asset_url( 'assets/dist/admin/index.css' ), array(), $this->get_asset_version( 'assets/dist/admin/index.css' ) );
		} else {
			wp_enqueue_style( 'alynt-es-admin-style', $this->get_asset_url( 'assets/css/admin-style.css' ), array(), $this->get_asset_version( 'assets/css/admin-style.css' ) );
		}

		if ( $this->asset_exists( 'assets/dist/admin/index.js' ) ) {
			wp_enqueue_script( 'alynt-es-admin-script', $this->get_asset_url( 'assets/dist/admin/index.js' ), array( 'jquery' ), $this->get_asset_version( 'assets/dist/admin/index.js' ), true );
		} else {
			wp_enqueue_script( 'alynt-es-admin-script', $this->get_asset_url( 'assets/js/admin-script.js' ), array( 'jquery' ), $this->get_asset_version( 'assets/js/admin-script.js' ), true );
		}

		wp_localize_script(
			'alynt-es-admin-script',
			'alyntESAdminConfig',
			array(
				'codeEditor' => array(
					'textareaId' => 'alynt_es_custom_css',
					'settings'   => $code_editor_settings,
				),
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
	 * Enqueues the unbundled frontend styles as a fallback when dist assets are unavailable.
	 *
	 * @return void
	 */
	private function enqueue_search_source_styles() {
		$this->enqueue_search_shell_styles();
		wp_enqueue_style( 'alynt-es-search-controls', $this->get_asset_url( 'assets/css/search-controls.css' ), array( 'alynt-es-search-shell' ), $this->get_asset_version( 'assets/css/search-controls.css' ) );
		wp_enqueue_style( 'alynt-es-search-results', $this->get_asset_url( 'assets/css/search-results.css' ), array( 'alynt-es-search-controls' ), $this->get_asset_version( 'assets/css/search-results.css' ) );
		wp_enqueue_style( 'alynt-es-search-responsive', $this->get_asset_url( 'assets/css/search-responsive.css' ), array( 'alynt-es-search-results' ), $this->get_asset_version( 'assets/css/search-responsive.css' ) );
	}

	/**
	 * Enqueues the lightweight shell stylesheet used by shortcode buttons and icons.
	 *
	 * @return void
	 */
	private function enqueue_search_shell_styles() {
		wp_enqueue_style( 'alynt-es-search-shell', $this->get_asset_url( 'assets/css/search-shell.css' ), array(), $this->get_asset_version( 'assets/css/search-shell.css' ) );
	}

	/**
	 * Enqueues the unbundled frontend scripts as a fallback when dist assets are unavailable.
	 *
	 * @return void
	 */
	private function enqueue_search_source_scripts() {
		wp_enqueue_script( 'alynt-es-search-utils', $this->get_asset_url( 'assets/js/search-utils.js' ), array( 'jquery' ), $this->get_asset_version( 'assets/js/search-utils.js' ), true );
		wp_enqueue_script( 'alynt-es-search-render', $this->get_asset_url( 'assets/js/search-render.js' ), array( 'jquery', 'alynt-es-search-utils' ), $this->get_asset_version( 'assets/js/search-render.js' ), true );
		wp_enqueue_script( 'alynt-es-search-api', $this->get_asset_url( 'assets/js/search-api.js' ), array( 'jquery', 'alynt-es-search-render' ), $this->get_asset_version( 'assets/js/search-api.js' ), true );
		wp_enqueue_script( 'alynt-es-search-events', $this->get_asset_url( 'assets/js/search-events.js' ), array( 'jquery', 'alynt-es-search-api' ), $this->get_asset_version( 'assets/js/search-events.js' ), true );
		wp_enqueue_script( 'alynt-es-search-init', $this->get_asset_url( 'assets/js/search-init.js' ), array( 'jquery', 'alynt-es-search-events' ), $this->get_asset_version( 'assets/js/search-init.js' ), true );
	}

	/**
	 * Checks whether a plugin asset exists on disk.
	 *
	 * @param string $relative_path Relative asset path.
	 *
	 * @return bool
	 */
	private function asset_exists( $relative_path ) {
		return file_exists( $this->get_asset_file_path( $relative_path ) );
	}

	/**
	 * Builds the public URL for a plugin asset.
	 *
	 * @param string $relative_path Relative asset path.
	 *
	 * @return string
	 */
	private function get_asset_url( $relative_path ) {
		return ALYNT_ES_PLUGIN_URL . ltrim( str_replace( '\\', '/', $relative_path ), '/' );
	}

	/**
	 * Resolves the cache-busting version string for a plugin asset.
	 *
	 * @param string $relative_path Relative asset path.
	 *
	 * @return string
	 */
	private function get_asset_version( $relative_path ) {
		$asset_file = $this->get_asset_file_path( $relative_path );

		if ( file_exists( $asset_file ) ) {
			return (string) filemtime( $asset_file );
		}

		return ALYNT_ES_VERSION;
	}

	/**
	 * Builds the absolute filesystem path for a plugin asset.
	 *
	 * @param string $relative_path Relative asset path.
	 *
	 * @return string
	 */
	private function get_asset_file_path( $relative_path ) {
		return ALYNT_ES_PLUGIN_DIR . str_replace( '/', DIRECTORY_SEPARATOR, ltrim( $relative_path, '/\\' ) );
	}

	/**
	 * Determines whether the shell stylesheet should be enqueued.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
}
