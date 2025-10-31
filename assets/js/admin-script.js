/**
 * Alynt Enhanced Search - Admin JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Color picker functionality
        $('.color-picker').on('input change', function() {
            var targetName = $(this).data('target');
            var colorValue = $(this).val();
            $('input[name="' + targetName + '"]').val(colorValue);
        });
        
        // Text input to color picker sync
        $('.color-text').on('input change', function() {
            var colorValue = $(this).val();
            var targetPicker = $(this).siblings('.color-picker');
            
            // Validate hex color format
            if (/^#[0-9A-Fa-f]{6}$/.test(colorValue)) {
                targetPicker.val(colorValue);
                $(this).removeClass('invalid');
            } else {
                $(this).addClass('invalid');
            }
        });
        
        // Initialize color pickers on page load
        $('.color-picker').each(function() {
            var targetName = $(this).data('target');
            var textInput = $('input[name="' + targetName + '"]');
            if (textInput.length) {
                $(this).val(textInput.val());
            }
        });
    });
    
})(jQuery);
