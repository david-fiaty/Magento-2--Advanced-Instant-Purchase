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
         * Load a button instance.
         */
        load: function (config) {
            // Set the instance config
            this.setConfig(config);
        },

        /**
         * Set a button instance config.
         */
        setConfig: function (config) {
            window.naxero = {};
            window.naxero.nbn = {};
            window.naxero.nbn.test = {};
            window.naxero.nbn.test[config.product.id] = config;

            // Load the button instances data container
            if (!NbnUtil.has(window, 'naxero.nbn.loaded', true)) {
                // Prepare the instance config
                var instances = {};
                instances[config.product.id] = config;

                // Build the config data
                window.naxero = {
                    nbn: {
                        loaded: true,
                        instances: instances,
                        current: config,
                        ui: {}
                    }
                };

                // Get the spinner HTML
                window.naxero.nbn.ui.loader = this.getSpinnerHtml();
            }
        },

        /**
         * Get a button instance config.
         */
        getConfig: function (productId) {
            return window.naxero.nbn[productId];
        },

        /**
         * Load the spinner icon HTML.
         */
        getSpinnerHtml: function () {
            // Get the spinner loaded flag
            var params = {
                data: {
                    url: window.naxero.nbn.current.ui.loader
                }
            };

            // Log the event
            NbnLogger.log(
                __('Loaded the spinner icon HTML'),
                params
            );

            return NbnTemplate.getSpinner(params);
        }
    }
});
