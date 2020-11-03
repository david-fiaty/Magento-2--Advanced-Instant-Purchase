define([
    'jquery',
    'mage/translate',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Naxero_BuyNow/js/view/helpers/slider',
    'Naxero_BuyNow/js/view/helpers/util',
    'Naxero_BuyNow/js/view/helpers/message',
    'Naxero_BuyNow/js/view/helpers/validation',
    'Naxero_BuyNow/js/view/helpers/logger'
], function ($, __, AdditionalValidators, AipSlider, AipUtil, AipMessage, AipValidation, AipLogger) {
    'use strict';

    AdditionalValidators.registerValidator(AipValidation);

    return {

        /**
         * Initialise the object.
         */
        init: function (obj) {
            this.o = obj;
            return this;
        },

        /**
         * Update the button states.
         */
        update() {
            $(this.submitButtonSelector).prop(
                'disabled',
                !AdditionalValidators.validate()
            );
        },

        /**
         * Set the additional validator events.
         */
        setValidationEvents() {
            // Fields value change event
            var self = this;
            $(AipValidation.inputSelectors).on('change', function () {
                self.update();
            });
        }
    };
});
