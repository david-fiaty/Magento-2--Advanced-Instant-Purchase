define([
    'mage/translate',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/template',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/logger',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/util'
], function(__, AipTemplate, AipLogger, AipUtil) {
    'use strict';

    return {
        /**
         * Initialise the object.
         */
        init: function() {
            return this;
        },

        /**
         * Load the spinner icon.
         */
        loadIcon: function() {
            if (!this.isSpinnerLoaded()) {
                // Build the spiner icon
                this.buildIcon();

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
        buildIcon: function() {
            // Get the spinner loaded flag
            var params = this.getLoadedFlag();

            // Load the rendered HTML
            this.o.loader = AipTemplate.getSpinner(params);

            // Log the event
            AipLogger.log(
                __('Loaded the spinner icon HTML'),
                params
            );
        },

        /**
         * Check if the HTML spinner is loaded.
         */
        isSpinnerLoaded: function() {
            return AipUtil.has(window, 'naxero.aip.spinner', true);
        },

        /**
         * Get the spinner loaded flag.
         */
        getLoadedFlag: function() {
            return {
                data: {
                    url: this.o.jsConfig.ui.loader
                }
            };
        }
    };
});
