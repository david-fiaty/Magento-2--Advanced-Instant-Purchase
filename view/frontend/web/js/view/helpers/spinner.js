define([
    'mage/translate',
    'Naxero_BuyNow/js/view/helpers/template',
    'Naxero_BuyNow/js/view/helpers/logger',
    'Naxero_BuyNow/js/view/helpers/util'
], function (__, AipTemplate, AipLogger, AipUtil) {
    'use strict';

    return {
        /**
         * Initialise the object.
         */
        init: function (obj) {
            this.o = obj;
            return this;
        },

        /**
         * Load the spinner icon.
         */
        loadIcon: function () {
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
        buildIcon: function () {
            // Get the spinner loaded flag
            var params = {
                data: {
                    url: this.o.jsConfig.ui.loader
                }
            };

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
        isSpinnerLoaded: function () {
            return AipUtil.has(window, 'naxero.aip.spinner', true);
        }
    };
});
