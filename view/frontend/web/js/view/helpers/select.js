define([
    'jquery',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/util',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/slider',
    'select2'
], function ($, AipUtil, AipSlider, select2) {
    'use strict';

    return {
        listSelector: '.aip-select',
        linkSelector: '.aip-new, .aip-plus-icon',

        /**
         * Create a login popup.
         */
        build: function(obj) {
            var self = this;

            // Initialise the select lists
            $(self.listSelector).select2({
                language: self.getLocale(),
                theme: 'classic',
                templateResult: AipUtil.formatIcon,
                templateSelection: AipUtil.formatIcon
            });

            // Set the lists events
            $(self.listSelector).on('change', function() {
                var targetField = $(this).attr('data-field');
                var fieldValue = $(this).data('field') == 'instant_purchase_payment_token'
                ? AipUtil.getOptionPublicHash(fieldValue)
                : fieldValue;
                $('input[name="' + targetField + '"]').val(fieldValue);
            });

            // Set the link events
            $(self.linkSelector).on('click', function(e) {
                obj.getForm(e);
                AipSlider.toggleView(e, obj);
            });
        },

        getLocale: function() {
            var locale = window.advancedInstantPurchase.user.language.split('_');
            return locale[0];
        }
    };
});
