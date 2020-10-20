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
        paymentMethodSelector: '#aip-payment-method-select',
        otherMethodsToggleSelector: '#aip-show-other-methods',
        otherMethodsSelector: '#aip-other-method-select',
        
        /**
         * Create a login popup.
         */
        build: function(obj) {
            // Initialise the select lists
            var self = this;
            $(self.listSelector).select2({
                language: self.getLocale(obj.jsConfig.user.language),
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

            // Other payment methods toggle
            $(self.otherMethodsSelector).prop('disabled', true);   
            $(self.otherMethodsToggleSelector).on('click touch', function() {
                // Other methods select state
                $(self.otherMethodsSelector).prop(
                    'disabled',
                    !$(this).is(':checked')
                );             

                // Saved cards select state
                $(self.paymentMethodSelector).prop(
                    'disabled',
                    $(this).is(':checked')
                );        
            });

            // Set the link events
            $(self.linkSelector).on('click touch', function(e) {
                AipSlider.toggleView(obj, e);
                obj.getForm(e);
            });
        },

        getLocale: function(code) {
            return code.split('_')[0];
        }
    };
});
