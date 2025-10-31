<?php
/**
 * AJAX handler for Alynt Enhanced Search
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Alynt_ES_Ajax_Handler {
    
    public function __construct() {
        add_action('wp_ajax_alynt_es_search', array($this, 'handle_search'));
        add_action('wp_ajax_nopriv_alynt_es_search', array($this, 'handle_search'));
    }
    
    /**
     * Handle AJAX search requests
     */
    public function handle_search() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'alynt_es_nonce')) {
            wp_die('Security check failed');
        }
        
        $search_query = sanitize_text_field($_POST['query']);
        $search_type = sanitize_text_field($_POST['type']); // 'products' or 'general'
        $page = intval($_POST['page']);
        
        $settings = get_option('alynt_es_settings');
        $results_per_page = $settings['results_per_page'];
        
        // Cache key for transient
        $cache_key = 'alynt_es_' . md5($search_query . $search_type . $page);
        $cached_results = get_transient($cache_key);
        
        if ($cached_results !== false) {
            wp_send_json_success($cached_results);
            return;
        }
        
        // Perform search
        $results = $this->perform_search($search_query, $search_type, $page, $results_per_page, $settings);
        
        // Cache results for 30 seconds
        set_transient($cache_key, $results, 30);
        
        wp_send_json_success($results);
    }
    
    /**
     * Perform the actual search
     */
    private function perform_search($query, $type, $page, $per_page, $settings) {
        $offset = ($page - 1) * $per_page;
        
        // Determine post types to search
        $post_types = $this->get_post_types_for_search($type, $settings);
        
        // Custom search that only searches title, content, and excerpt
        add_filter('posts_search', array($this, 'custom_search_filter'), 10, 2);
        add_filter('posts_orderby', array($this, 'custom_search_orderby'), 10, 2);
        
        $args = array(
            's' => $query,
            'post_type' => $post_types,
            'post_status' => 'publish',
            'posts_per_page' => $per_page,
            'offset' => $offset,
            'meta_query' => array(),
            'suppress_filters' => false
        );
        
        // Exclude password protected posts
        $args['has_password'] = false;
        
        // For products, ensure they're in stock if WooCommerce is active
        if ($type === 'products' && class_exists('WooCommerce')) {
            $args['meta_query'][] = array(
                'key' => '_stock_status',
                'value' => 'instock',
                'compare' => '='
            );
        }
        
        $search_query = new WP_Query($args);
        
        // Remove filters after search
        remove_filter('posts_search', array($this, 'custom_search_filter'), 10);
        remove_filter('posts_orderby', array($this, 'custom_search_orderby'), 10);
        
        // Get total count for pagination
        $total_args = $args;
        $total_args['posts_per_page'] = -1;
        $total_args['fields'] = 'ids';
        $total_query = new WP_Query($total_args);
        $total_posts = $total_query->found_posts;
        
        $results = array(
            'posts' => array(),
            'pagination' => $this->generate_pagination($page, $per_page, $total_posts),
            'total' => $total_posts,
            'search_term' => $query
        );
        
        if ($search_query->have_posts()) {
            while ($search_query->have_posts()) {
                $search_query->the_post();
                $results['posts'][] = $this->format_post_data(get_post(), $type, $settings);
            }
            wp_reset_postdata();
        }
        
        return $results;
    }
    
    /**
     * Get post types for search based on type and settings
     */
    private function get_post_types_for_search($type, $settings) {
        $enabled_post_types = $settings['post_types'];
        
        if ($type === 'products') {
            return array_intersect($enabled_post_types, array('product'));
        } else {
            // General pages - exclude products
            return array_diff($enabled_post_types, array('product'));
        }
    }
    
    /**
     * Format post data for frontend
     */
    private function format_post_data($post, $type, $settings) {
        $data = array(
            'id' => $post->ID,
            'title' => html_entity_decode(get_the_title($post->ID), ENT_QUOTES, 'UTF-8'),
            'url' => get_permalink($post->ID),
            'excerpt' => '',
            'featured_image' => '',
            'categories' => array(),
            'type' => $type
        );
        
        // Get excerpt
        if ($settings['show_excerpt']) {
            $excerpt = get_the_excerpt($post->ID);
            if (empty($excerpt)) {
                $excerpt = wp_trim_words(get_the_content(null, false, $post->ID), $settings['excerpt_length']);
            } else {
                $excerpt = wp_trim_words($excerpt, $settings['excerpt_length']);
            }
            // Strip HTML tags and decode entities while preserving special characters like em dashes and ellipses
            $data['excerpt'] = html_entity_decode(strip_tags($excerpt), ENT_QUOTES, 'UTF-8');
        }
        
        // Get featured image
        $show_images = ($type === 'products') ? $settings['show_featured_images_products'] : $settings['show_featured_images_general'];
        if ($show_images && has_post_thumbnail($post->ID)) {
            $image_size = ($type === 'products') ? array(300, 300) : 'medium';
            $data['featured_image'] = get_the_post_thumbnail_url($post->ID, $image_size);
        }
        
        // Get categories/terms
        if ($type === 'products' && function_exists('wc_get_product')) {
            $product = wc_get_product($post->ID);
            if ($product) {
                $terms = get_the_terms($post->ID, 'product_cat');
                if ($terms && !is_wp_error($terms)) {
                    foreach ($terms as $term) {
                        $data['categories'][] = array(
                            'name' => $term->name,
                            'slug' => $term->slug
                        );
                    }
                }
            }
        } else {
            // For general pages, get categories or tags
            $taxonomy = ($post->post_type === 'post') ? 'category' : 'post_tag';
            $terms = get_the_terms($post->ID, $taxonomy);
            if ($terms && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $data['categories'][] = array(
                        'name' => $term->name,
                        'slug' => $term->slug
                    );
                }
            }
        }
        
        return $data;
    }
    
    /**
     * Generate pagination data
     */
    private function generate_pagination($current_page, $per_page, $total_posts) {
        $total_pages = ceil($total_posts / $per_page);
        
        if ($total_pages <= 1) {
            return array();
        }
        
        $pagination = array();
        $range = 2; // Show 2 pages on each side of current page
        
        // Previous button
        if ($current_page > 1) {
            $pagination[] = array(
                'type' => 'prev',
                'page' => $current_page - 1,
                'text' => '‹',
                'aria_label' => __('Previous page', 'alynt-enhanced-search')
            );
        }
        
        // Page numbers
        $start = max(1, $current_page - $range);
        $end = min($total_pages, $current_page + $range);
        
        // Add first page if not in range
        if ($start > 1) {
            $pagination[] = array(
                'type' => 'page',
                'page' => 1,
                'text' => '1',
                'current' => false
            );
            
            if ($start > 2) {
                $pagination[] = array(
                    'type' => 'ellipsis',
                    'text' => '...'
                );
            }
        }
        
        // Add page range
        for ($i = $start; $i <= $end; $i++) {
            $pagination[] = array(
                'type' => 'page',
                'page' => $i,
                'text' => (string)$i,
                'current' => ($i === $current_page)
            );
        }
        
        // Add last page if not in range
        if ($end < $total_pages) {
            if ($end < $total_pages - 1) {
                $pagination[] = array(
                    'type' => 'ellipsis',
                    'text' => '...'
                );
            }
            
            $pagination[] = array(
                'type' => 'page',
                'page' => $total_pages,
                'text' => (string)$total_pages,
                'current' => false
            );
        }
        
        // Next button
        if ($current_page < $total_pages) {
            $pagination[] = array(
                'type' => 'next',
                'page' => $current_page + 1,
                'text' => '›',
                'aria_label' => __('Next page', 'alynt-enhanced-search')
            );
        }
        
        return $pagination;
    }
    
    /**
     * Custom search filter to only search title, content, and excerpt
     */
    public function custom_search_filter($search, $wp_query) {
        global $wpdb;
        
        if (empty($search) || !$wp_query->is_search()) {
            return $search;
        }
        
        $search_term = $wp_query->get('s');
        if (empty($search_term)) {
            return $search;
        }
        
        // Escape the search term
        $search_term = $wpdb->esc_like($search_term);
        $search_term = '%' . $search_term . '%';
        
        // Build custom search query for title, content, and excerpt only
        $search = " AND (
            ({$wpdb->posts}.post_title LIKE %s) OR 
            ({$wpdb->posts}.post_content LIKE %s) OR 
            ({$wpdb->posts}.post_excerpt LIKE %s)
        )";
        
        // Prepare the query with the search term
        $search = $wpdb->prepare($search, $search_term, $search_term, $search_term);
        
        return $search;
    }
    
    /**
     * Custom orderby for search relevance
     */
    public function custom_search_orderby($orderby, $wp_query) {
        global $wpdb;
        
        if (!$wp_query->is_search()) {
            return $orderby;
        }
        
        $search_term = $wp_query->get('s');
        if (empty($search_term)) {
            return $orderby;
        }
        
        // Escape the search term
        $search_term = $wpdb->esc_like($search_term);
        $search_term = '%' . $search_term . '%';
        
        // Order by relevance: title matches first, then content, then excerpt
        $orderby = $wpdb->prepare("
            CASE 
                WHEN {$wpdb->posts}.post_title LIKE %s THEN 1
                WHEN {$wpdb->posts}.post_content LIKE %s THEN 2  
                WHEN {$wpdb->posts}.post_excerpt LIKE %s THEN 3
                ELSE 4
            END ASC,
            {$wpdb->posts}.post_date DESC
        ", $search_term, $search_term, $search_term);
        
        return $orderby;
    }
}
