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
    'mage/validation',
    'domReady!'
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

            // Validate the product options
            if (condition1) {
                // Form validation
                var formSelector = '#xxx';
                $(formSelector).validation();

                // Field validation
                return $(formSelector).validation('isValid');
            }

            return true;
        }
    }
});
