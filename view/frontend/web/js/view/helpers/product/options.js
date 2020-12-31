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
    'jquery'
], function ($) {
    'use strict';

    return {
        /**
         * Set product options events.
         */
        initOptionsEvents: function (productId) {
            var options = this.getOptions(productId);
            if (options && options.length > 0) {
                for (var i = 0; i < options.length; i++) {
                    NbnProductOptions.getOptionHandler(options[i]['type']).initOptionEvent(options[i]);
                }
            }
        },

        /**
         * Get a product options.
         */
        getOptions: function (productId) {
            return window.naxero.nbn.instances[productId].product.options;
        },

        /**
         * Get the option handler component.
         */
        getOptionHandler: function (optionType) {
            
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
                    var error = this.getOptionHandler(option[i]['type'])
                    .getOptionErrors(option[i], e)
                    .length > 0;

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
