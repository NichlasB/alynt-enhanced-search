<?php
/**
 * AJAX handler for Alynt Enhanced Search.
 *
 * @package    Alynt_Enhanced_Search
 * @subpackage Alynt_Enhanced_Search/includes
 * @since      1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles AJAX search requests from the front end.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */
class Alynt_ES_Ajax_Handler {

	/**
	 * Search query service instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Alynt_ES_Search_Query_Service
	 */
	private $query_service;

	/**
	 * Constructor. Registers AJAX action hooks.
	 *
	 * @since 1.0.0
	 *
	 * @param Alynt_ES_Search_Query_Service|null $query_service Search query service instance.
	 */
	public function __construct( $query_service = null ) {
		$this->query_service = $query_service;

		if ( ! $this->query_service ) {
			$formatter           = new Alynt_ES_Search_Result_Formatter();
			$pagination_builder  = new Alynt_ES_Pagination_Builder();
			$this->query_service = new Alynt_ES_Search_Query_Service( $formatter, $pagination_builder );
		}

		add_action( 'wp_ajax_alynt_es_search', array( $this, 'handle_search' ) );
		add_action( 'wp_ajax_nopriv_alynt_es_search', array( $this, 'handle_search' ) );
	}

	/**
	 * Processes the AJAX search request, checks cache, runs search, and returns JSON results.
	 *
	 * Caches successful results as transients for 30 seconds.
	 * Sends a JSON success response with results, or a 403 JSON error on nonce failure.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function handle_search() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'alynt_es_nonce' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Your session expired. Refresh the page and try your search again.', 'alynt-enhanced-search' ),
					'code'    => 'session_expired',
				),
				403
			);
		}

		$rate_limit_key = 'alynt_es_rl_' . md5( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'unknown' );
		$rate_limit_count = (int) get_transient( $rate_limit_key );

		if ( $rate_limit_count > 30 ) {
			wp_send_json_error(
				array(
					'message' => __( 'Too many search requests. Please wait a moment and try again.', 'alynt-enhanced-search' ),
					'code'    => 'rate_limited',
				),
				429
			);
		}

		set_transient( $rate_limit_key, $rate_limit_count + 1, 60 );

		if ( is_user_logged_in() && ! current_user_can( 'read' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Permission denied.', 'alynt-enhanced-search' ),
				),
				403
			);
		}

		$search_query = isset( $_POST['query'] ) ? mb_substr( trim( sanitize_text_field( wp_unslash( $_POST['query'] ) ) ), 0, 200 ) : '';
		$search_type  = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : 'general';
		$page         = isset( $_POST['page'] ) ? min( 1000, max( 1, intval( wp_unslash( $_POST['page'] ) ) ) ) : 1;

		if ( 'products' !== $search_type ) {
			$search_type = 'general';
		}

		$settings         = Alynt_ES_Search_Settings::get_settings();
		$results_per_page = (int) $settings['results_per_page'];

		if ( '' === $search_query ) {
			wp_send_json_success( $this->query_service->perform_search( $search_query, $search_type, $page, $results_per_page, $settings ) );
		}

		$cache_key      = Alynt_ES_Search_Cache_Manager::get_cache_key( $search_query, $search_type, $page );
		$cached_results = get_transient( $cache_key );

		if ( false !== $cached_results ) {
			wp_send_json_success( $cached_results );
		}

		$results = $this->query_service->perform_search( $search_query, $search_type, $page, $results_per_page, $settings );

		if ( is_wp_error( $results ) ) {
			wp_send_json_error(
				array(
					'message' => $results->get_error_message(),
					'code'    => $results->get_error_code(),
				),
				500
			);
		}

		if ( false !== set_transient( $cache_key, $results, 30 ) ) {
			Alynt_ES_Search_Cache_Manager::register_cache_key( $cache_key );
		}

		wp_send_json_success( $results );
	}
}
