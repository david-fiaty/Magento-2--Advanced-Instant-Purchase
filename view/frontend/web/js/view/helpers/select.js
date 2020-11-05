define([
    'jquery',
    'Naxero_BuyNow/js/view/helpers/util',
    'Naxero_BuyNow/js/view/helpers/slider',
    'Naxero_BuyNow/js/view/helpers/address',
    'Naxero_BuyNow/js/view/helpers/payment',
    'select2'
], function ($, BnUtil, BnSlider, BnAddress, BnPayment, select2) {
    'use strict';

    return {
        listSelector: '.nbn-select',
        linkSelector: '.nbn-new, .nbn-plus-icon',
        paymentMethodSelector: '#nbn-payment-method-select',
        otherMethodsToggleSelector: '#nbn-show-other-methods',
        otherMethodsSelector: '#nbn-other-method-select',
        addressLinkSelector: '.nbn-address-link',
        cardLinkSelector: '.nbn-card-link',
        
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
            $(this.listSelector).select2({
                language: self.getLocale(this.o.jsConfig.user.language),
                theme: 'classic',
                templateResult: BnUtil.formatIcon,
                templateSelection: BnUtil.formatIcon
            });

            // Set the lists events
            $(this.listSelector).on('change', function () {
                // Get the current field value
                var thisFieldValue = $(this).val();

                // Set the new field value
                var newFieldValue = $(this).data('field') == 'instant_purchase_payment_token'
                ? BnUtil.getOptionPublicHash(thisFieldValue)
                : thisFieldValue;

                // Update the hidden target field value
                var targetField = $(this).attr('data-field');
                $('input[name="' + targetField + '"]').val(newFieldValue);
            });

            // Other payment methods toggle
            $(this.otherMethodsSelector).prop('disabled', true);
            $(this.otherMethodsToggleSelector).on('click touch', function () {
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

            // Set the new address link event
            $(this.addressLinkSelector).on('click touch', function (e) {
                BnSlider.toggleView(e);
                BnAddress.getAddressForm(self.o, e);
            });

            // Set the new card link event
            $(this.cardLinkSelector).on('click touch', function (e) {
                BnSlider.toggleView(e);
                BnPayment.getCardForm(self.o, e);
            });
        },

        getLocale: function (code) {
            return code.split('_')[0];
        }
    };
});
