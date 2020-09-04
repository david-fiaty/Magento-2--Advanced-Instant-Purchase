define([
    'jquery',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/util',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/slider',
    'select2'
], function ($, AiiUtil, AiiSlider, select2) {
    'use strict';

    return {
        listSelector: '.aii-select',
        linkSelector: '.aii-new',

        /**
         * Create a login popup.
         */
        build: function(obj) {
            var self = this;

            // Initialise the select lists
            $(self.listSelector).select2({
                language: 'en',
                theme: 'classic',
                templateResult: AiiUtil.formatIcon,
                templateSelection: AiiUtil.formatIcon
            });

            // Set the lists events
            $(self.listSelector).on('change', function() {
                var targetField = $(this).attr('data-field');
                var fieldValue = $(this).data('field') == 'instant_purchase_payment_token'
                ? AiiUtil.getOptionPublicHash(fieldValue)
                : fieldValue;
                $('input[name="' + targetField + '"]').val(fieldValue);
            });

            // Set the link events
            $(self.linkSelector).on('click', function(e) {
                AiiSlider.toggleView(e, obj);
            });
        }
    };
});
