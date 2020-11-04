define([
    'jquery',
    'Naxero_BuyNow/js/view/helpers/util',
    'Naxero_BuyNow/js/view/helpers/slider',
    'Naxero_BuyNow/js/view/helpers/address',
    'select2'
], function ($, AipUtil, AipSlider, AipAddress, select2) {
    'use strict';

    return {
        listSelector: '.aip-select',
        linkSelector: '.aip-new, .aip-plus-icon',
        paymentMethodSelector: '#aip-payment-method-select',
        otherMethodsToggleSelector: '#aip-show-other-methods',
        otherMethodsSelector: '#aip-other-method-select',
        addressLinkSelector: '.aip-address-link',
        
        /**
         * Initialise the object.
         */
        init: function (obj) {
            this.o = obj;
            return this;
        },

        /**
         * Create a login popup.
         */
        build: function () {
            // Initialise the select lists
            var self = this;
            $(self.listSelector).select2({
                language: self.getLocale(this.o.jsConfig.user.language),
                theme: 'classic',
                templateResult: AipUtil.formatIcon,
                templateSelection: AipUtil.formatIcon
            });

            // Set the lists events
            $(self.listSelector).on('change', function () {
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
            $(self.otherMethodsToggleSelector).on('click touch', function () {
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

            // Set the address link events
            $(self.addressLinkSelector).on('click touch', function (e) {
                AipSlider.toggleView(e);
                AipAddress.getAddressForm(e);
            });
        },

        getLocale: function (code) {
            return code.split('_')[0];
        }
    };
});
