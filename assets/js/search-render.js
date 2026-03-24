(function($, window) {
    'use strict';

    window.alyntESRender = {
        renderResults: function(data) {
            const state = window.alyntESState;
            const config = window.alyntESConfig || {};
            const utils = window.alyntESUtils;
            const $results = $('.alynt-es-results');
            const $pagination = $('.alynt-es-pagination');

            $results.empty();
            $pagination.empty();

            if (data.posts && data.posts.length > 0) {
                const $grid = $('<div class="alynt-es-results-grid"></div>');

                data.posts.forEach(function(post) {
                    $grid.append(window.alyntESRender.createResultCard(post, config, utils));
                });

                $results.append($grid);

                if (data.search_term && data.search_term.length > 0) {
                    window.alyntESRender.highlightSearchTerms($grid, data.search_term, utils);
                }

                const resultText = data.total === 1
                    ? config.i18n.oneResultFound
                    : config.i18n.multipleResultsFound.replace('%d', data.total);
                $results.prepend('<div class="screen-reader-text" aria-live="polite">' + resultText + '</div>');
            } else if (state.currentQuery.length > 0) {
                const noResultsText = config.i18n.noResults.replace('%s', utils.escapeHtml(state.currentQuery));
                $results.html('<div class="alynt-es-no-results"><p>' + noResultsText + '</p></div>');
            } else {
                $results.html('<div class="alynt-es-no-query"><p>' + config.i18n.noQuery + '</p></div>');
            }

            if (data.pagination && data.pagination.length > 0) {
                window.alyntESRender.renderPagination(data.pagination);
            }

            if (typeof window.alyntESState.afterRender === 'function') {
                window.alyntESState.afterRender();
                window.alyntESState.afterRender = null;
            }
        },

        createResultCard: function(post, config, utils) {
            let cardHtml = '<a href="' + utils.escapeHtml(post.url) + '" class="alynt-es-result-card" aria-label="' + utils.escapeHtml(post.title) + '">';

            if (post.featured_image) {
                const imageClass = post.type === 'products' ? 'product-image' : '';
                cardHtml += '<img src="' + utils.escapeHtml(post.featured_image) + '" alt="" class="alynt-es-card-image ' + imageClass + '" loading="lazy">';
            }

            cardHtml += '<div class="alynt-es-card-content">';

            if (post.categories && post.categories.length > 0) {
                cardHtml += '<div class="alynt-es-card-categories">';
                post.categories.forEach(function(category) {
                    cardHtml += '<span class="alynt-es-card-category">' + utils.escapeHtml(category.name) + '</span>';
                });
                cardHtml += '</div>';
            }

            cardHtml += '<h2 class="alynt-es-card-title">' + utils.escapeHtml(post.title) + '</h2>';

            if (post.excerpt && config.showExcerpt) {
                cardHtml += '<div class="alynt-es-card-excerpt-container"><p class="alynt-es-card-excerpt">' + utils.escapeHtml(post.excerpt) + '</p></div>';
            }

            cardHtml += '</div></a>';

            return $(cardHtml);
        },

        renderPagination: function(paginationData) {
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
        },

        showError: function(message, showRetry) {
            const utils = window.alyntESUtils;
            const config = window.alyntESConfig || {};
            let errorHtml = '<div class="alynt-es-error" role="alert"><p style="color: #d32f2f; text-align: center; padding: 2rem 2rem 1rem;">' + utils.escapeHtml(message) + '</p>';

            if (showRetry) {
                errorHtml += '<p style="text-align: center; padding: 0 2rem 2rem;"><button type="button" class="alynt-es-retry button">' + utils.escapeHtml(config.i18n.retrySearch) + '</button></p>';
            }

            errorHtml += '</div>';
            $('.alynt-es-results').html(errorHtml);
        },

        highlightSearchTerms: function($container, searchTerm, utils) {
            if (!searchTerm || searchTerm.length < 2) {
                return;
            }

            const regex = new RegExp('(' + utils.escapeRegex(searchTerm) + ')', 'gi');

            $container.find('.alynt-es-card-title, .alynt-es-card-excerpt').each(function() {
                const $element = $(this);
                const originalHtml = $element.html();
                const highlightedHtml = originalHtml.replace(regex, '<mark class="alynt-es-highlight">$1</mark>');

                if (highlightedHtml !== originalHtml) {
                    $element.html(highlightedHtml);
                }
            });
        }
    };
})(jQuery, window);
