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

    window.alyntESApi = {
        performSearch: function(isInitial) {
            const state = window.alyntESState;
            const config = window.alyntESConfig || {};

            if (state.isLoading) {
                return;
            }

            const $results = $('.alynt-es-results');
            const $loading = $('.alynt-es-loading');

            state.isLoading = true;
            state.lastSearchWasInitial = isInitial;
            $loading.show();
            $results.attr('aria-busy', 'true');
            setSubmitState(true, config);

            $.ajax({
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
                    if (response.success) {
                        window.alyntESRender.renderResults(response.data);
                        return;
                    }

                    window.alyntESRender.showError(config.i18n.searchFailed, true);
                },
                error: function(response, textStatus) {
                    const message = getErrorMessage(response, textStatus, config);
                    window.alyntESRender.showError(message, true);
                },
                complete: function() {
                    state.isLoading = false;
                    $loading.hide();
                    $results.attr('aria-busy', 'false');
                    setSubmitState(false, config);
                }
            });
        }
    };
})(jQuery, window);
