define([
    'mage/translate',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/template',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/logger',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/util'
], function (__, AipTemplate, AipLogger, AipUtil) {
    'use strict';

    return {
        /**
         * Load the spinner icon.
         */
        loadIcon: function(obj) {
            if (!this.isSpinnerLoaded()) {
                // Build the spiner icon
                this.buildIcon(obj);

                // Set the spinner loaded flag
                window.naxero = {
                    aip: {
                        spinner: true
                    }
                };
            }
        },

        /**
         * Build the spinner icon.
         */
        buildIcon: function(obj) {
            // Prepare the loader template parameters
            var params = {
                data: {
                    url: obj.jsConfig.ui.loader
                }
            };

            // Load the rendered HTML
            obj.loader = AipTemplate.getSpinner(params);

            // Log the event
            AipLogger.log(
                obj,
                __('Loaded the spinner icon HTML'),
                params
            );
        },

        /**
         * Check if the HTML spinner is loaded.
         */
        isSpinnerLoaded: function() {
            return AipUtil.has(window, 'naxero.aip.spinner', true);
        }
    };
});