<?php
/**
 * Pagination builder for Alynt Enhanced Search.
 *
 * @package    Alynt_Enhanced_Search
 * @subpackage Alynt_Enhanced_Search/includes
 * @since      1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Builds a pagination data structure for search results.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */
class Alynt_ES_Pagination_Builder {

	/**
	 * Builds the pagination array for a given result set.
	 *
	 * @since 1.0.0
	 *
	 * @param int $current_page The current page number (1-based).
	 * @param int $per_page     Number of results per page.
	 * @param int $total_posts  Total number of matching posts.
	 *
	 * @return array Pagination data including page links, previous/next buttons, and total pages.
	 */
    public function build($current_page, $per_page, $total_posts) {
        $total_pages = (int) ceil($total_posts / $per_page);

        if ($total_pages <= 1) {
            return array();
        }

        $pagination = array();
        $range = 2;
        $start = max(1, $current_page - $range);
        $end = min($total_pages, $current_page + $range);

        $this->append_previous_link($pagination, $current_page);
        $this->append_leading_pages($pagination, $start);
        $this->append_page_window($pagination, $start, $end, $current_page);
        $this->append_trailing_pages($pagination, $end, $total_pages);
        $this->append_next_link($pagination, $current_page, $total_pages);

        return $pagination;
    }

    private function append_previous_link(&$pagination, $current_page) {
        if ($current_page <= 1) {
            return;
        }

        $pagination[] = array(
            'type' => 'prev',
            'page' => $current_page - 1,
            'text' => '‹',
            'aria_label' => __('Previous page', 'alynt-enhanced-search')
        );
    }

    private function append_leading_pages(&$pagination, $start) {
        if ($start <= 1) {
            return;
        }

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

    private function append_page_window(&$pagination, $start, $end, $current_page) {
        for ($page_number = $start; $page_number <= $end; $page_number++) {
            $pagination[] = array(
                'type' => 'page',
                'page' => $page_number,
                'text' => (string) $page_number,
                'current' => ($page_number === $current_page)
            );
        }
    }

    private function append_trailing_pages(&$pagination, $end, $total_pages) {
        if ($end >= $total_pages) {
            return;
        }

        if ($end < $total_pages - 1) {
            $pagination[] = array(
                'type' => 'ellipsis',
                'text' => '...'
            );
        }

        $pagination[] = array(
            'type' => 'page',
            'page' => $total_pages,
            'text' => (string) $total_pages,
            'current' => false
        );
    }

    private function append_next_link(&$pagination, $current_page, $total_pages) {
        if ($current_page >= $total_pages) {
            return;
        }

        $pagination[] = array(
            'type' => 'next',
            'page' => $current_page + 1,
            'text' => '›',
            'aria_label' => __('Next page', 'alynt-enhanced-search')
        );
    }
}
