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
        load: function(config) {
            // Set the instance config
            this.setConfig(config);

            // Spinner icon
            NbnSpinner.loadIcon(config);
        },

        /**
         * Set a button instance config.
         */
        setConfig: function(config) {
            this.prepareConfig();
            window.naxero.nbn[config.product.id] = config;
        },

        /**
         * Get a button instance config.
         */
        getConfig: function(productId) {
            return window.naxero.nbn[productId];
        },

        /**
         * Prepare the instance config.
         */
        prepareConfig: function () {
            // Load the spinner icon
            if (!NbnUtil.has(window, 'naxero.nbn.spinner', true)) {
                this.loadSpinnerIcon();
            }

            if (!NbnUtil.has(window, 'naxero.nbn.instances')) {
                window.naxero = {
                    nbn: {
                        instances: []
                    }
                };
            }
        },

        /**
         * Load the spinner icon.
         */
        loadSpinnerIcon: function () {
            // Get the spinner loaded flag
            var params = {
                data: {
                    url: this.config.ui.loader
                }
            };

            // Load the rendered HTML
            NbnLoader = NbnTemplate.getSpinner(params);

            // Log the event
            NbnLogger.log(
                __('Loaded the spinner icon HTML'),
                params
            );
        }
    }
});
