<?php
// phpcs:ignoreFile WordPress.Files.FileName.InvalidClassFileName
/**
 * Search query service for Alynt Enhanced Search.
 *
 * @package    Alynt_Enhanced_Search
 * @subpackage Alynt_Enhanced_Search/includes
 * @since      1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles search query execution and result assembly.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */
class Alynt_ES_Search_Query_Service {

	/**
	 * Search result formatter instance.
	 *
	 * @var Alynt_ES_Search_Result_Formatter
	 */
	private $formatter;

	/**
	 * Pagination builder instance.
	 *
	 * @var Alynt_ES_Pagination_Builder
	 */
	private $pagination_builder;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Alynt_ES_Search_Result_Formatter $formatter          Result formatter instance.
	 * @param Alynt_ES_Pagination_Builder      $pagination_builder Pagination builder instance.
	 */
	public function __construct( $formatter, $pagination_builder ) {
		$this->formatter          = $formatter;
		$this->pagination_builder = $pagination_builder;
	}

	/**
	 * Executes a search query and returns the formatted results payload.
	 *
	 * @since 1.0.0
	 *
	 * @param string $query    The search query string.
	 * @param string $type     Content type filter ('all', 'products', or a post type slug).
	 * @param int    $page     Current page number.
	 * @param int    $per_page Number of results per page.
	 * @param array  $settings Current plugin settings.
	 *
	 * @return array|WP_Error Results payload including posts, pagination, and totals.
	 */
	public function perform_search( $query, $type, $page, $per_page, $settings ) {
		$query = trim( $query );

		if ( $query === '' ) {
			return array(
				'posts'       => array(),
				'pagination'  => array(),
				'total'       => 0,
				'search_term' => '',
			);
		}

		$args = $this->build_search_args( $query, $type, $page, $per_page, $settings );

		if ( empty( $args['post_type'] ) ) {
			return array(
				'posts'       => array(),
				'pagination'  => array(),
				'total'       => 0,
				'search_term' => $query,
			);
		}

		add_filter( 'posts_search', array( $this, 'custom_search_filter' ), 10, 2 );
		add_filter( 'posts_orderby', array( $this, 'custom_search_orderby' ), 10, 2 );

		try {
			$search_query = $this->run_query( $args, 'search_results' );
			if ( is_wp_error( $search_query ) ) {
				return $search_query;
			}

			$total_posts = (int) $search_query->found_posts;

			return $this->build_results_payload( $search_query, $query, $type, $page, $per_page, $total_posts, $settings );
		} finally {
			remove_filter( 'posts_search', array( $this, 'custom_search_filter' ), 10 );
			remove_filter( 'posts_orderby', array( $this, 'custom_search_orderby' ), 10 );
		}
	}

	/**
	 * Builds the WordPress query arguments for a search request.
	 *
	 * @param string $query    Sanitized search query.
	 * @param string $type     Requested search type.
	 * @param int    $page     Current results page.
	 * @param int    $per_page Results per page.
	 * @param array  $settings Current plugin settings.
	 *
	 * @return array
	 */
	private function build_search_args( $query, $type, $page, $per_page, $settings ) {
		$args = array(
			's'                   => $query,
			'alynt_es_search'     => true,
			'post_type'           => $this->get_post_types_for_search( $type, $settings ),
			'post_status'         => 'publish',
			'posts_per_page'      => $per_page,
			'paged'               => $page,
			'suppress_filters'    => false,
			'has_password'        => false,
			'ignore_sticky_posts' => true,
		);

		if ( $type === 'products' && class_exists( 'WooCommerce' ) ) {
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Required to limit product searches to in-stock items.
			$args['meta_query'] = array(
				array(
					'key'     => '_stock_status',
					'value'   => 'instock',
					'compare' => '=',
				),
			);
		}

		return $args;
	}

	/**
	 * Builds the normalized API payload for a search result set.
	 *
	 * @param WP_Query $search_query Search query instance.
	 * @param string   $query        Search term.
	 * @param string   $type         Requested search type.
	 * @param int      $page         Current results page.
	 * @param int      $per_page     Results per page.
	 * @param int      $total_posts  Total matching posts.
	 * @param array    $settings     Current plugin settings.
	 *
	 * @return array
	 */
	private function build_results_payload( $search_query, $query, $type, $page, $per_page, $total_posts, $settings ) {
		$results = array(
			'posts'       => array(),
			'pagination'  => $this->pagination_builder->build( $page, $per_page, $total_posts ),
			'total'       => $total_posts,
			'search_term' => $query,
		);

		if ( ! $search_query->have_posts() ) {
			return $results;
		}

		while ( $search_query->have_posts() ) {
			$search_query->the_post();
			$results['posts'][] = $this->formatter->format_post_data( get_post(), $type, $settings );
		}

		wp_reset_postdata();

		return $results;
	}

	/**
	 * Resolves which post types should be searched for the requested type.
	 *
	 * @param string $type     Requested search type.
	 * @param array  $settings Current plugin settings.
	 *
	 * @return array
	 */
	private function get_post_types_for_search( $type, $settings ) {
		$enabled_post_types = $settings['post_types'];

		if ( $type === 'products' ) {
			return array_values( array_intersect( $enabled_post_types, array( 'product' ) ) );
		}

		return array_values( array_diff( $enabled_post_types, array( 'product' ) ) );
	}

	/**
	 * Executes a WordPress query and converts low-level failures into WP_Error objects.
	 *
	 * @param array  $args    Query arguments.
	 * @param string $context Logging context.
	 *
	 * @return WP_Query|WP_Error
	 */
	private function run_query( $args, $context ) {
		global $wpdb;

		$wpdb->last_error = '';

		try {
			$query = new WP_Query( $args );
		} catch ( Throwable $throwable ) {
			$this->log_query_failure( $context, $throwable->getMessage() );

			return new WP_Error(
				'alynt_es_search_query_failed',
				__( 'We could not load search results. Please refresh the page and try again.', 'alynt-enhanced-search' )
			);
		}

		if ( ! empty( $wpdb->last_error ) ) {
			$this->log_query_failure( $context, $wpdb->last_error );

			return new WP_Error(
				'alynt_es_search_query_failed',
				__( 'We could not load search results. Please refresh the page and try again.', 'alynt-enhanced-search' )
			);
		}

		return $query;
	}

	/**
	 * Logs a search-query failure for debugging purposes.
	 *
	 * @param string $context Failure context label.
	 * @param string $details Error details.
	 *
	 * @return void
	 */
	private function log_query_failure( $context, $details ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Search query failures should be captured in site logs for diagnosis.
		error_log( sprintf( '[Alynt Enhanced Search] Search query failed (%s): %s', $context, mb_substr( $details, 0, 200 ) ) );
	}

	/**
	 * Filters the SQL WHERE clause to search post title and content.
	 *
	 * Registered temporarily during perform_search() only.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $search   The WHERE clause for the search.
	 * @param WP_Query $wp_query The WP_Query instance.
	 *
	 * @return string Modified WHERE clause.
	 */
	public function custom_search_filter( $search, $wp_query ) {
		global $wpdb;

		if ( empty( $search ) || ! $wp_query->is_search() || ! $wp_query->get( 'alynt_es_search' ) ) {
			return $search;
		}

		$search_term = $wp_query->get( 's' );

		if ( empty( $search_term ) ) {
			return $search;
		}

		$search_term = '%' . $wpdb->esc_like( $search_term ) . '%';

		return $wpdb->prepare(
			" AND (
	            ({$wpdb->posts}.post_title LIKE %s) OR
	            ({$wpdb->posts}.post_content LIKE %s) OR
	            ({$wpdb->posts}.post_excerpt LIKE %s)
	        )",
			$search_term,
			$search_term,
			$search_term
		);
	}

	/**
	 * Filters the SQL ORDER BY clause to prioritize title matches.
	 *
	 * Registered temporarily during perform_search() only.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $orderby  The ORDER BY clause.
	 * @param WP_Query $wp_query The WP_Query instance.
	 *
	 * @return string Modified ORDER BY clause.
	 */
	public function custom_search_orderby( $orderby, $wp_query ) {
		global $wpdb;

		if ( ! $wp_query->is_search() || ! $wp_query->get( 'alynt_es_search' ) ) {
			return $orderby;
		}

		$search_term = $wp_query->get( 's' );

		if ( empty( $search_term ) ) {
			return $orderby;
		}

		$title_prefix_match   = $wpdb->esc_like( $search_term ) . '%';
		$title_contains_match = '%' . $wpdb->esc_like( $search_term ) . '%';

		return $wpdb->prepare(
			"CASE
                WHEN {$wpdb->posts}.post_title LIKE %s THEN 1
                WHEN {$wpdb->posts}.post_title LIKE %s THEN 2
                ELSE 3
            END ASC,
            {$wpdb->posts}.post_date DESC",
			$title_prefix_match,
			$title_contains_match
		);
	}
}
