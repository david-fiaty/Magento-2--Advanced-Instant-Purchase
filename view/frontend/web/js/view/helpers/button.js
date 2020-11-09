/**
 * Naxero.com
 * Professional ecommerce integrations for Magento.
 *
 * PHP version 7
 *
 * @category  Magento2
 * @package   Naxero
 * @author    Platforms Development Team <contact@naxero.com>
 * @copyright © Naxero.com all rights reserved
 * @license   https://opensource.org/licenses/mit-license.html MIT License
 * @link      https://www.naxero.com
 */

 define([
    'jquery',
    'mage/translate',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Naxero_BuyNow/js/view/helpers/validation',
], function ($, __, AdditionalValidators, NbnValidation) {
    'use strict';

    AdditionalValidators.registerValidator(NbnValidation);

    return {
        submitButtonSelector: '.aip-submit',

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
