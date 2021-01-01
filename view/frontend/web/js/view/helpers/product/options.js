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
    'mage/mage'
], function ($) {
    'use strict';

    return {
        /**
         * Get a product options.
         */
        getOptions: function (productId) {
            return window.naxero.nbn.instances[productId].product.options;
        },

        /**
         * Product custom options validation.
         */
        validateOptions: function (productId) {
            // Prepare variables
            var options = this.getOptions(productId);
            var condition1 = options && options.length > 0;
            var errors = 0;

            // Loop through the product options
            if (condition1) {
                for (var i = 0; i < options.length; i++) {
                    // Validate the option
                    var fieldSelector = '.product-custom-option[name="option[' + options[i].option_id + ']"]';
                    $(fieldSelector).validation();
                    var error = !$(fieldSelector).validation('isValid');
                    console.log('validateOptions');
                    console.log(error);

                    // Register the error
                    if (error) {
                        errors++;
                    }
                }

                return errors == 0;
            }

            return true;
        }
    }
});
