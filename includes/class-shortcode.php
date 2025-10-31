<?php
/**
 * Shortcode functionality for Alynt Enhanced Search
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Alynt_ES_Shortcode {
    
    public function __construct() {
        add_shortcode('alynt_es_search', array($this, 'render_shortcode'));
    }
    
    /**
     * Render the search shortcode
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML output
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
        } else {
            return $this->render_button_search($search_url, $atts['text'], $additional_classes);
        }
    }
    
    /**
     * Render button-style search
     */
    private function render_button_search($search_url, $text, $additional_classes) {
        ob_start();
        ?>
        <div class="alynt-es-shortcode-wrapper<?php echo $additional_classes; ?>">
            <a href="<?php echo esc_url($search_url); ?>" class="alynt-es-search-button" role="button" aria-label="<?php echo esc_attr($text); ?>">
                <?php echo esc_html($text); ?>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render icon-style search
     */
    private function render_icon_search($search_url, $additional_classes) {
        ob_start();
        ?>
        <div class="alynt-es-shortcode-wrapper<?php echo $additional_classes; ?>">
            <a href="<?php echo esc_url($search_url); ?>" class="alynt-es-search-icon" aria-label="<?php esc_attr_e('Search', 'alynt-enhanced-search'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true">
                    <path d="M21 21L16.514 16.506L21 21ZM19 10.5C19 15.194 15.194 19 10.5 19C5.806 19 2 15.194 2 10.5C2 5.806 5.806 2 10.5 2C15.194 2 19 5.806 19 10.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }
}
