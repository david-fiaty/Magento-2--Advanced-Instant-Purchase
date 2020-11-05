define([
    'jquery',
    'mage/translate',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Naxero_BuyNow/js/view/helpers/validation',
], function ($, __, AdditionalValidators, NbnValidation) {
    'use strict';

    AdditionalValidators.registerValidator(NbnValidation);

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
            $(NbnValidation.inputSelectors).on('change', function () {
                self.update();
            });
        }
    };
});
