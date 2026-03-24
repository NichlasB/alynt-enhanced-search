(function(window) {
    'use strict';

    window.alyntESUtils = {
        escapeRegex: function(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        },
        escapeHtml: function(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };

            return String(text).replace(/[&<>"']/g, function(match) {
                return map[match];
            });
        }
    };
})(window);
