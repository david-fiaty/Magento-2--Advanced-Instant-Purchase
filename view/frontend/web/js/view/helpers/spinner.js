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
    'mage/translate',
    'Naxero_BuyNow/js/view/helpers/template',
    'Naxero_BuyNow/js/view/helpers/logger',
    'Naxero_BuyNow/js/view/helpers/util'
], function (__, NbnTemplate, NbnLogger, NbnUtil) {
    'use strict';

    return {
        /**
         * Load the spinner icon.
         */
        loadIcon: function () {
            if (!this.isSpinnerLoaded()) {
                // Build the spiner icon
                this.buildIcon();

                // Set the spinner loaded flag
                window.naxero = {
                    nbn: {
                        spinner: true
                    }
                };
            }
        },

        /**
         * Build the spinner icon.
         */
        buildIcon: function () {
            // Get the spinner loaded flag
            var params = {
                data: {
                    url: this.o.config.ui.loader
                }
            };

            // Load the rendered HTML
            this.o.loader = NbnTemplate.getSpinner(params);

            // Log the event
            NbnLogger.log(
                __('Loaded the spinner icon HTML'),
                params
            );
        },

        /**
         * Check if the HTML spinner is loaded.
         */
        isSpinnerLoaded: function () {
            return NbnUtil.has(window, 'naxero.nbn.spinner', true);
        }
    };
});
