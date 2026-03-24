(function($) {
    'use strict';

    function getConfig() {
        return window.alyntESAdminConfig || { i18n: {} };
    }

    function getErrorId($input) {
        return $input.attr('id') + '-error';
    }

    function showFieldError($input, message) {
        const errorId = getErrorId($input);
        let $error = $('#' + errorId);

        if (!$error.length) {
            $error = $('<p class="alynt-es-field-error" id="' + errorId + '"></p>');
            $input.after($error);
        }

        $error.text(message).show();
        $input.addClass('invalid').attr('aria-invalid', 'true');

        const describedBy = ($input.attr('aria-describedby') || '').split(' ').filter(Boolean);
        if (describedBy.indexOf(errorId) === -1) {
            describedBy.push(errorId);
            $input.attr('aria-describedby', describedBy.join(' '));
        }
    }

    function clearFieldError($input) {
        const errorId = getErrorId($input);
        $('#' + errorId).remove();
        $input.removeClass('invalid').removeAttr('aria-invalid');

        const describedBy = ($input.attr('aria-describedby') || '').split(' ').filter(function(value) {
            return value && value !== errorId;
        });

        if (describedBy.length) {
            $input.attr('aria-describedby', describedBy.join(' '));
        } else {
            $input.removeAttr('aria-describedby');
        }
    }

    function validateColorInput($input) {
        const colorValue = $input.val();
        const config = getConfig();

        if (/^#[0-9A-Fa-f]{6}$/.test(colorValue)) {
            clearFieldError($input);
            return true;
        }

        showFieldError($input, config.i18n.invalidColor);
        return false;
    }

    $(document).ready(function() {
        const config = getConfig();
        const $form = $('.alynt-es-settings-form');
        let isDirty = false;

        $('.color-picker').on('input change', function() {
            const targetName = $(this).data('target');
            const colorValue = $(this).val();
            const $targetInput = $('input[name="' + targetName + '"]');
            $targetInput.val(colorValue);
            clearFieldError($targetInput);
            isDirty = true;
        });

        $('.color-text').on('input change blur', function() {
            const $input = $(this);
            const colorValue = $input.val();
            const targetPicker = $input.siblings('.color-picker');

            if (validateColorInput($input)) {
                targetPicker.val(colorValue);
            }

            isDirty = true;
        });

        $('.color-picker').each(function() {
            const targetName = $(this).data('target');
            const textInput = $('input[name="' + targetName + '"]');

            if (textInput.length) {
                $(this).val(textInput.val());
            }
        });

        $form.on('input change', 'input, select, textarea', function() {
            isDirty = true;
        });

        $form.on('submit', function(event) {
            let firstInvalidField = null;

            $('.color-text').each(function() {
                const $input = $(this);
                if (!validateColorInput($input) && !firstInvalidField) {
                    firstInvalidField = $input;
                }
            });

            if (firstInvalidField) {
                event.preventDefault();
                firstInvalidField.trigger('focus');
                return;
            }

            isDirty = false;
        });

        $(window).on('beforeunload', function() {
            if (!isDirty) {
                return undefined;
            }

            return config.i18n.unsavedChanges;
        });
    });
})(jQuery);
