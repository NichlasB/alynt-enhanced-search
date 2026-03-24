<?php
/**
 * Shortcode functionality for Alynt Enhanced Search.
 *
 * @package    Alynt_Enhanced_Search
 * @subpackage Alynt_Enhanced_Search/public
 * @since      1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registers and renders the [alynt_es_search] shortcode.
 *
 * @package Alynt_Enhanced_Search
 * @since   1.0.0
 */
class Alynt_ES_Shortcode {

	/**
	 * Constructor. Registers the alynt_es_search shortcode.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
        add_shortcode('alynt_es_search', array($this, 'render_shortcode'));
    }

	/**
	 * Renders the search shortcode output.
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts {
	 *     Optional. Shortcode attributes.
	 *
	 *     @type string $type  Display type. 'button' or 'icon'. Default 'button'.
	 *     @type string $text  Button label text. Default 'Search'.
	 *     @type string $class Additional CSS classes. Default ''.
	 * }
	 *
	 * @return string Rendered shortcode HTML.
	 */
	public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'type' => 'button',
            'text' => __('Search', 'alynt-enhanced-search'),
            'class' => ''
        ), $atts, 'alynt_es_search');

        $search_url = home_url('/?s=');
        $additional_classes = !empty($atts['class']) ? ' ' . esc_attr($atts['class']) : '';

        if ($atts['type'] === 'icon') {
            return $this->render_icon_search($search_url, $additional_classes);
        }

        return $this->render_button_search($search_url, $atts['text'], $additional_classes);
    }

    private function render_button_search($search_url, $text, $additional_classes) {
        ob_start();
        ?>
        <div class="alynt-es-shortcode-wrapper<?php echo esc_attr( $additional_classes ); ?>">
            <a href="<?php echo esc_url($search_url); ?>" class="alynt-es-search-button">
                <?php echo esc_html($text); ?>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_icon_search($search_url, $additional_classes) {
        ob_start();
        ?>
        <div class="alynt-es-shortcode-wrapper<?php echo esc_attr( $additional_classes ); ?>">
            <a href="<?php echo esc_url($search_url); ?>" class="alynt-es-search-icon" aria-label="<?php esc_attr_e('Search', 'alynt-enhanced-search'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M21 21L16.514 16.506L21 21ZM19 10.5C19 15.194 15.194 19 10.5 19C5.806 19 2 15.194 2 10.5C2 5.806 5.806 2 10.5 2C15.194 2 19 5.806 19 10.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }
}
