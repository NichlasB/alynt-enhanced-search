<?php
/**
 * Search result formatter for Alynt Enhanced Search.
 *
 * @package    Alynt_Enhanced_Search
 * @subpackage Alynt_Enhanced_Search/includes
 * @since      1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Formats raw WP_Post objects into structured search result arrays.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */
class Alynt_ES_Search_Result_Formatter {

	/**
	 * Formats a single post into a structured search result array.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post     The post object to format.
	 * @param string  $type     The content type context (e.g. 'product', 'post').
	 * @param array   $settings Current plugin settings.
	 *
	 * @return array Formatted result with keys: id, title, url, excerpt, featured_image, categories, type.
	 */
    public function format_post_data($post, $type, $settings) {
        return array(
            'id' => $post->ID,
            'title' => html_entity_decode(get_the_title($post->ID), ENT_QUOTES, 'UTF-8'),
            'url' => get_permalink($post->ID),
            'excerpt' => $this->build_excerpt($post, $settings),
            'featured_image' => $this->resolve_featured_image($post, $type, $settings),
            'categories' => $this->resolve_terms($post, $type),
            'type' => $type
        );
    }

    private function build_excerpt($post, $settings) {
        if (empty($settings['show_excerpt'])) {
            return '';
        }

        $excerpt = get_the_excerpt($post->ID);

        if (empty($excerpt)) {
            $excerpt = wp_trim_words(get_the_content(null, false, $post->ID), $settings['excerpt_length']);
        } else {
            $excerpt = wp_trim_words($excerpt, $settings['excerpt_length']);
        }

        return html_entity_decode(strip_tags($excerpt), ENT_QUOTES, 'UTF-8');
    }

    private function resolve_featured_image($post, $type, $settings) {
        $show_images = ($type === 'products') ? $settings['show_featured_images_products'] : $settings['show_featured_images_general'];

        if (!$show_images || !has_post_thumbnail($post->ID)) {
            return '';
        }

        $image_size = ($type === 'products') ? array(300, 300) : 'medium';

        return get_the_post_thumbnail_url($post->ID, $image_size);
    }

    private function resolve_terms($post, $type) {
        if ($type === 'products' && function_exists('wc_get_product')) {
            return $this->resolve_product_terms($post);
        }

        return $this->resolve_general_terms($post);
    }

    private function resolve_product_terms($post) {
        $product = wc_get_product($post->ID);

        if (!$product) {
            return array();
        }

        return $this->map_terms(get_the_terms($post->ID, 'product_cat'));
    }

    private function resolve_general_terms($post) {
        $taxonomy = ($post->post_type === 'post') ? 'category' : 'post_tag';

        return $this->map_terms(get_the_terms($post->ID, $taxonomy));
    }

    private function map_terms($terms) {
        if (!$terms || is_wp_error($terms)) {
            return array();
        }

        $mapped_terms = array();

        foreach ($terms as $term) {
            $mapped_terms[] = array(
                'name' => $term->name,
                'slug' => $term->slug
            );
        }

        return $mapped_terms;
    }
}
