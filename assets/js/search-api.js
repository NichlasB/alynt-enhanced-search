(function($, window) {
    'use strict';

    function getErrorMessage(response, textStatus, config) {
        if (!window.navigator.onLine) {
            return config.i18n.networkError;
        }

        if (textStatus === 'timeout') {
            return config.i18n.timeoutError;
        }

        if (response && response.responseJSON && response.responseJSON.data) {
            if (response.responseJSON.data.code === 'session_expired') {
                return config.i18n.sessionExpired;
            }

            if (response.responseJSON.data.message) {
                return response.responseJSON.data.message;
            }
        }

        if (response && response.status >= 500) {
            return config.i18n.serverError;
        }

        return config.i18n.searchFailed;
    }

    function setSubmitState(isLoading, config) {
        const $submit = $('.alynt-es-search-submit');

        $submit.prop('disabled', isLoading);
        $submit.attr('aria-disabled', isLoading ? 'true' : 'false');
        $submit.attr('aria-busy', isLoading ? 'true' : 'false');
        $submit.attr('aria-label', isLoading ? config.i18n.searching : config.i18n.submitSearch);
    }

    function renderEmptyState() {
        window.alyntESRender.renderResults({
            posts: [],
            pagination: [],
            total: 0,
            search_term: ''
        });
    }

    window.alyntESApi = {
        performSearch: function(isInitial) {
            const state = window.alyntESState;
            const config = window.alyntESConfig || {};
            const $results = $('.alynt-es-results');
            const $loading = $('.alynt-es-loading');

            state.currentQuery = (state.currentQuery || '').trim();

            if (state.request && state.request.readyState !== 4) {
                state.request.abort();
            }

            if (!state.currentQuery.length) {
                state.isLoading = false;
                state.lastSearchWasInitial = isInitial;
                state.request = null;
                $loading.hide();
                $results.attr('aria-busy', 'false');
                setSubmitState(false, config);
                renderEmptyState();
                return;
            }

            state.isLoading = true;
            state.lastSearchWasInitial = isInitial;
            $loading.show();
            $results.attr('aria-busy', 'true');
            setSubmitState(true, config);

            const request = $.ajax({
                url: config.ajaxUrl,
                type: 'POST',
                dataType: 'json',
                timeout: config.requestTimeout || 10000,
                data: {
                    action: 'alynt_es_search',
                    nonce: config.nonce,
                    query: state.currentQuery,
                    type: state.currentType,
                    page: state.currentPage
                },
                success: function(response) {
                    if (state.request !== request) {
                        return;
                    }

                    if (response.success) {
                        window.alyntESRender.renderResults(response.data);
                        return;
                    }

                    window.alyntESRender.showError(config.i18n.searchFailed, true);
                },
                error: function(response, textStatus) {
                    if (textStatus === 'abort' || state.request !== request) {
                        return;
                    }

                    const isSessionExpired = response && response.responseJSON && response.responseJSON.data && response.responseJSON.data.code === 'session_expired';
                    const message = getErrorMessage(response, textStatus, config);
                    window.alyntESRender.showError(message, !isSessionExpired);
                },
                complete: function() {
                    if (state.request !== request) {
                        return;
                    }

                    state.request = null;
                    state.isLoading = false;
                    $loading.hide();
                    $results.attr('aria-busy', 'false');
                    setSubmitState(false, config);
                }
            });

            state.request = request;
        }
    };
})(jQuery, window);
