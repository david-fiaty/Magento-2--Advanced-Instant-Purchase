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
                language: self.getLocale(obj.user.language),
                theme: 'classic',
                templateResult: AipUtil.formatIcon,
                templateSelection: AipUtil.formatIcon
            });

            // Set the lists events
            $(self.listSelector).on('change', function() {
                // Get the current field value
                var thisFieldValue = $(this).val();

                // Set the new field value
                var newFieldValue = $(this).data('field') == 'instant_purchase_payment_token'
                ? AipUtil.getOptionPublicHash(thisFieldValue)
                : thisFieldValue;   

                // Update the hidden target field value
                var targetField = $(this).attr('data-field');
                $('input[name="' + targetField + '"]').val(newFieldValue);
            });

            // Set the link events
            $(self.linkSelector).on('click', function(e) {
                AipSlider.toggleView(obj, e);
                obj.getForm(e);
            });
        },

        getLocale: function(code) {
            return code.split('_')[0];
        }
    };
});
