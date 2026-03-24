(function($, window) {
    'use strict';

    function initializeSearch() {
        const config = window.alyntESConfig || {};

        window.alyntESState = {
            searchTimeout: null,
            currentPage: 1,
            currentQuery: '',
            currentType: (config.woocommerceEnabled && !config.initialQuery) ? 'products' : 'general',
            isLoading: false,
            request: null,
            afterRender: null,
            lastSearchWasInitial: false
        };

        window.alyntESEvents.bindEvents();

        if (config.initialQuery) {
            window.alyntESState.currentQuery = config.initialQuery;
            window.alyntESApi.performSearch(true);
        }
    }

    $(document).ready(function() {
        initializeSearch();
    });
})(jQuery, window);
