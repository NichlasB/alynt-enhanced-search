/**
 * Alynt Enhanced Search - Frontend JavaScript
 * Handles AJAX search functionality with debouncing and accessibility
 */

(function($) {
    'use strict';
    
    let searchTimeout;
    let currentPage = 1;
    let currentQuery = '';
    let currentType = 'products';
    let isLoading = false;
    
    $(document).ready(function() {
        initializeSearch();
    });
    
    function initializeSearch() {
        // Get settings from global variable
        const settings = window.alyntESSettings || {};
        
        // Set initial search type based on WooCommerce availability
        if (!settings.woocommerceEnabled) {
            currentType = 'general';
        }
        
        // Initialize event listeners
        bindEvents();
        
        // Perform initial search if query exists
        if (settings.initialQuery) {
            currentQuery = settings.initialQuery;
            performSearch(true);
        }
    }
    
    function bindEvents() {
        // Search input with debouncing
        $(document).on('input', '.alynt-es-search-input', function() {
            const query = $(this).val().trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length >= 3 || query.length === 0) {
                searchTimeout = setTimeout(function() {
                    currentQuery = query;
                    currentPage = 1;
                    performSearch();
                }, alynt_es_ajax.search_delay || 300);
            }
        });
        
        // Search form submission
        $(document).on('submit', '.alynt-es-search-form', function(e) {
            e.preventDefault();
            const query = $('.alynt-es-search-input').val().trim();
            currentQuery = query;
            currentPage = 1;
            performSearch();
        });
        
        // Toggle pills for WooCommerce
        $(document).on('click', '.alynt-es-toggle-pill', function() {
            const $pill = $(this);
            const type = $pill.data('type');
            
            if (type !== currentType) {
                // Update active state
                $('.alynt-es-toggle-pill').removeClass('active').attr('aria-selected', 'false');
                $pill.addClass('active').attr('aria-selected', 'true');
                
                // Update current type and reset page
                currentType = type;
                currentPage = 1;
                
                // Perform search with new type
                performSearch();
            }
        });
        
        // Pagination clicks
        $(document).on('click', '.alynt-es-pagination-item[data-page]', function(e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'));
            
            if (page && page !== currentPage && !isLoading) {
                currentPage = page;
                performSearch();
                
                // Scroll to top of results
                $('html, body').animate({
                    scrollTop: $('.alynt-es-results').offset().top - 100
                }, 300);
            }
        });
        
        // Keyboard navigation for accessibility
        $(document).on('keydown', '.alynt-es-toggle-pill, .alynt-es-pagination-item', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $(this).click();
            }
        });
    }
    
    function performSearch(isInitial = false) {
        if (isLoading) return;
        
        const $results = $('.alynt-es-results');
        const $loading = $('.alynt-es-loading');
        const $pagination = $('.alynt-es-pagination');
        
        // Show loading state
        isLoading = true;
        $loading.show();
        
        if (!isInitial) {
            $results.attr('aria-busy', 'true');
        }
        
        // Prepare AJAX data
        const ajaxData = {
            action: 'alynt_es_search',
            nonce: alynt_es_ajax.nonce,
            query: currentQuery,
            type: currentType,
            page: currentPage
        };
        
        // Perform AJAX request
        $.ajax({
            url: alynt_es_ajax.ajax_url,
            type: 'POST',
            data: ajaxData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderResults(response.data);
                } else {
                    showError('Search failed. Please try again.');
                }
            },
            error: function() {
                showError('Network error. Please check your connection and try again.');
            },
            complete: function() {
                isLoading = false;
                $loading.hide();
                $results.attr('aria-busy', 'false');
            }
        });
    }
    
    function renderResults(data) {
        const $results = $('.alynt-es-results');
        const $pagination = $('.alynt-es-pagination');
        
        // Clear existing content
        $results.empty();
        $pagination.empty();
        
        if (data.posts && data.posts.length > 0) {
            // Create results grid
            const $grid = $('<div class="alynt-es-results-grid"></div>');
            
            data.posts.forEach(function(post) {
                const $card = createResultCard(post, data.search_term);
                $grid.append($card);
            });
            
            $results.append($grid);
            
            // Highlight search terms in the results
            if (data.search_term && data.search_term.length > 0) {
                highlightSearchTerms($grid, data.search_term);
            }
            
            // Add results announcement for screen readers
            const resultText = data.total === 1 
                ? '1 result found' 
                : data.total + ' results found';
            $results.prepend('<div class="screen-reader-text" aria-live="polite">' + resultText + '</div>');
            
        } else if (currentQuery.length > 0) {
            // No results found
            $results.html('<div class="alynt-es-no-results"><p>No results found for "' + escapeHtml(currentQuery) + '". Try different keywords.</p></div>');
        } else {
            // No query entered
            $results.html('<div class="alynt-es-no-query"><p>Enter a search term to find content.</p></div>');
        }
        
        // Render pagination
        if (data.pagination && data.pagination.length > 0) {
            renderPagination(data.pagination);
        }
    }
    
    function createResultCard(post, searchTerm) {
        const settings = window.alyntESSettings || {};
        let cardHtml = '<a href="' + escapeHtml(post.url) + '" class="alynt-es-result-card" aria-label="' + escapeHtml(post.title) + '">';
        
        // Featured image
        if (post.featured_image) {
            const imageClass = post.type === 'products' ? 'product-image' : '';
            cardHtml += '<img src="' + escapeHtml(post.featured_image) + '" alt="" class="alynt-es-card-image ' + imageClass + '" loading="lazy">';
        }
        
        cardHtml += '<div class="alynt-es-card-content">';
        
        // Categories (for products)
        if (post.categories && post.categories.length > 0) {
            cardHtml += '<div class="alynt-es-card-categories">';
            post.categories.forEach(function(category) {
                cardHtml += '<span class="alynt-es-card-category">' + escapeHtml(category.name) + '</span>';
            });
            cardHtml += '</div>';
        }
        
        // Title
        cardHtml += '<h3 class="alynt-es-card-title">' + escapeHtml(post.title) + '</h3>';
        
        // Excerpt
        if (post.excerpt && settings.showExcerpt) {
            cardHtml += '<div class="alynt-es-card-excerpt-container"><p class="alynt-es-card-excerpt">' + escapeHtml(post.excerpt) + '</p></div>';
        }
        
        cardHtml += '</div></a>';
        
        return $(cardHtml);
    }
    
    function renderPagination(paginationData) {
        const $pagination = $('.alynt-es-pagination');
        
        paginationData.forEach(function(item) {
            let $item;
            
            if (item.type === 'ellipsis') {
                $item = $('<span class="alynt-es-pagination-item ellipsis">' + item.text + '</span>');
            } else {
                $item = $('<button class="alynt-es-pagination-item" data-page="' + item.page + '">' + item.text + '</button>');
                
                if (item.current) {
                    $item.addClass('current').attr('aria-current', 'page');
                }
                
                if (item.aria_label) {
                    $item.attr('aria-label', item.aria_label);
                }
            }
            
            $pagination.append($item);
        });
    }
    
    function showError(message) {
        const $results = $('.alynt-es-results');
        $results.html('<div class="alynt-es-error"><p style="color: #d32f2f; text-align: center; padding: 2rem;">' + escapeHtml(message) + '</p></div>');
    }
    
    function highlightSearchTerms($container, searchTerm) {
        if (!searchTerm || searchTerm.length < 2) return;
        
        // Create a regex for case-insensitive matching
        const regex = new RegExp('(' + escapeRegex(searchTerm) + ')', 'gi');
        
        // Find all text nodes in titles and excerpts
        $container.find('.alynt-es-card-title, .alynt-es-card-excerpt').each(function() {
            const $element = $(this);
            const originalHtml = $element.html();
            
            // Replace matches with highlighted spans
            const highlightedHtml = originalHtml.replace(regex, '<mark class="alynt-es-highlight">$1</mark>');
            
            if (highlightedHtml !== originalHtml) {
                $element.html(highlightedHtml);
            }
        });
    }
    
    function escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
    
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
})(jQuery);
