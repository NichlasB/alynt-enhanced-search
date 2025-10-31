<?php
/**
 * Custom search template for Alynt Enhanced Search
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$settings = Alynt_ES_Search_Template::get_settings();
$woocommerce_enabled = Alynt_ES_Search_Template::is_woocommerce_enabled();
$search_query = get_search_query();

get_header(); ?>

<div class="alynt-es-search-page">
    <div class="alynt-es-container">
        
        <!-- Main Heading -->
        <div class="alynt-es-header">
            <h1 class="alynt-es-main-title"><?php esc_html_e('Looking for Something?', 'alynt-enhanced-search'); ?></h1>
        </div>
        
        <?php if ($woocommerce_enabled): ?>
        <!-- WooCommerce Toggle Pills -->
        <div class="alynt-es-toggle-wrapper">
            <div class="alynt-es-toggle-pills" role="tablist" aria-label="<?php esc_attr_e('Search Type', 'alynt-enhanced-search'); ?>">
                <button 
                    class="alynt-es-toggle-pill active" 
                    data-type="products" 
                    role="tab" 
                    aria-selected="true" 
                    aria-controls="alynt-es-results"
                    id="products-tab">
                    <?php esc_html_e('Products', 'alynt-enhanced-search'); ?>
                </button>
                <button 
                    class="alynt-es-toggle-pill" 
                    data-type="general" 
                    role="tab" 
                    aria-selected="false" 
                    aria-controls="alynt-es-results"
                    id="general-tab">
                    <?php esc_html_e('General Pages', 'alynt-enhanced-search'); ?>
                </button>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Search Bar -->
        <div class="alynt-es-search-wrapper">
            <form class="alynt-es-search-form" role="search" aria-label="<?php esc_attr_e('Search', 'alynt-enhanced-search'); ?>">
                <input 
                    type="search" 
                    class="alynt-es-search-input" 
                    placeholder="<?php esc_attr_e('Search...', 'alynt-enhanced-search'); ?>" 
                    value="<?php echo esc_attr($search_query); ?>"
                    aria-label="<?php esc_attr_e('Search query', 'alynt-enhanced-search'); ?>"
                    autocomplete="off">
                <button type="submit" class="alynt-es-search-submit" aria-label="<?php esc_attr_e('Submit search', 'alynt-enhanced-search'); ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M21 21L16.514 16.506L21 21ZM19 10.5C19 15.194 15.194 19 10.5 19C5.806 19 2 15.194 2 10.5C2 5.806 5.806 2 10.5 2C15.194 2 19 5.806 19 10.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </form>
        </div>
        
        <!-- Loading Indicator -->
        <div class="alynt-es-loading" style="display: none;" aria-live="polite" aria-label="<?php esc_attr_e('Loading search results', 'alynt-enhanced-search'); ?>">
            <div class="alynt-es-spinner"></div>
            <span class="screen-reader-text"><?php esc_html_e('Loading...', 'alynt-enhanced-search'); ?></span>
        </div>
        
        <!-- Results Container -->
        <div 
            class="alynt-es-results" 
            id="alynt-es-results" 
            role="region" 
            aria-live="polite" 
            aria-label="<?php esc_attr_e('Search results', 'alynt-enhanced-search'); ?>"
            data-columns="<?php echo esc_attr($settings['max_columns']); ?>">
            
            <?php if ($search_query): ?>
                <!-- Initial results will be loaded here via AJAX -->
                <div class="alynt-es-initial-load">
                    <?php esc_html_e('Loading results...', 'alynt-enhanced-search'); ?>
                </div>
            <?php else: ?>
                <div class="alynt-es-no-query">
                    <p><?php esc_html_e('Enter a search term to find content.', 'alynt-enhanced-search'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination Container -->
        <div class="alynt-es-pagination-wrapper">
            <nav class="alynt-es-pagination" role="navigation" aria-label="<?php esc_attr_e('Search results pagination', 'alynt-enhanced-search'); ?>">
                <!-- Pagination will be loaded here via AJAX -->
            </nav>
        </div>
        
    </div>
</div>

<!-- Custom CSS from settings -->
<?php if (!empty($settings['custom_css'])): ?>
<style type="text/css">
<?php echo wp_strip_all_tags($settings['custom_css']); ?>
</style>
<?php endif; ?>

<script type="text/javascript">
// Pass settings to JavaScript
window.alyntESSettings = {
    woocommerceEnabled: <?php echo $woocommerce_enabled ? 'true' : 'false'; ?>,
    resultsPerPage: <?php echo intval($settings['results_per_page']); ?>,
    showExcerpt: <?php echo $settings['show_excerpt'] ? 'true' : 'false'; ?>,
    excerptLength: <?php echo intval($settings['excerpt_length']); ?>,
    maxColumns: <?php echo intval($settings['max_columns']); ?>,
    showFeaturedImagesGeneral: <?php echo $settings['show_featured_images_general'] ? 'true' : 'false'; ?>,
    showFeaturedImagesProducts: <?php echo $settings['show_featured_images_products'] ? 'true' : 'false'; ?>,
    initialQuery: '<?php echo esc_js($search_query); ?>'
};
</script>

<?php get_footer(); ?>
