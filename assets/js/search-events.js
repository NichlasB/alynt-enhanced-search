(function($, window) {
    'use strict';

    function bindSearchInput() {
        $(document).on('input', '.alynt-es-search-input', function() {
            const query = $(this).val().trim();
            const config = window.alyntESConfig || {};

            clearTimeout(window.alyntESState.searchTimeout);

            if (query.length >= 3 || query.length === 0) {
                window.alyntESState.searchTimeout = setTimeout(function() {
                    window.alyntESState.currentQuery = query;
                    window.alyntESState.currentPage = 1;
                    window.alyntESApi.performSearch(false);
                }, config.searchDelay || 300);
            }
        });
    }

    function bindFormSubmit() {
        $(document).on('submit', '.alynt-es-search-form', function(event) {
            event.preventDefault();
            window.alyntESState.currentQuery = $('.alynt-es-search-input').val().trim();
            window.alyntESState.currentPage = 1;
            window.alyntESApi.performSearch(false);
        });
    }

    function bindTypeToggle() {
        $(document).on('click', '.alynt-es-toggle-pill', function() {
            const $pill = $(this);
            const type = $pill.data('type');

            if (type === window.alyntESState.currentType) {
                return;
            }

            $('.alynt-es-toggle-pill').removeClass('active').attr('aria-selected', 'false');
            $pill.addClass('active').attr('aria-selected', 'true');

            window.alyntESState.currentType = type;
            window.alyntESState.currentPage = 1;
            window.alyntESApi.performSearch(false);
        });
    }

    function bindPagination() {
        $(document).on('click', '.alynt-es-pagination-item[data-page]', function(event) {
            event.preventDefault();
            const page = parseInt($(this).data('page'), 10);

            if (!page || page === window.alyntESState.currentPage || window.alyntESState.isLoading) {
                return;
            }

            window.alyntESState.currentPage = page;
            window.alyntESState.afterRender = function() {
                var $firstCard = $('.alynt-es-result-card').first();
                if ($firstCard.length) {
                    $firstCard.attr('tabindex', '-1').focus();
                }
            };
            window.alyntESApi.performSearch(false);

            $('html, body').animate({
                scrollTop: $('.alynt-es-results').offset().top - 100
            }, 300);
        });
    }

    function bindKeyboardSupport() {
        $(document).on('keydown', '.alynt-es-toggle-pill, .alynt-es-pagination-item, .alynt-es-retry', function(event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                $(this).click();
            }
        });
    }

    function bindRetry() {
        $(document).on('click', '.alynt-es-retry', function() {
            window.alyntESApi.performSearch(window.alyntESState.lastSearchWasInitial);
        });
    }

    window.alyntESEvents = {
        bindEvents: function() {
            bindSearchInput();
            bindFormSubmit();
            bindTypeToggle();
            bindPagination();
            bindKeyboardSupport();
            bindRetry();
        }
    };
})(jQuery, window);
