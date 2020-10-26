define([
    'jquery',
    'mage/translate',
    'mage/url',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/modal',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/tree',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/slider'
], function ($, __, UrlBuilder, AipModal, AipTree, AipSlider) {
    'use strict';

    return {
        logCount: 1,
        logTitleCss: 'font-weight: bold; color: blue;',
        logViewerButtonClass: 'aip-ui-logger-button',
        logsUrl: 'naxero-aip/ajax/logs',

        /**
         * Log data to the browser console.
         */
        log: function(obj, msg, data) {
            // Default data value
            data = data || null;

            // Handle the logging display
            if (this.isConsoleLoggingEnabled(obj)) {
                this.logToConsole(obj, msg, data);
            }
        },

        /**
         * Handle the data console logging logic.
         */
        logToConsole: function(obj, msg, data) {
            // Log title
            console.log(
                this.getLogTitle(obj),
                this.logTitleCss
            );
                
            // Log the event message
            console.log(msg)

            // Log event data
            if (data) console.log(data);

            // Log count
            this.logCount++;
        },
 
        /**
         * Check if console logging is enabled.
         */
        isConsoleLoggingEnabled: function(obj) {
            return obj.jsConfig.general.debug_enabled
            && obj.jsConfig.general.console_logging_enabled;
        },

        /**
         * Get a log title.
         */
        getLogTitle: function(obj) {
            return '%c[' + this.logCount + '][' + obj.jsConfig.module.title + ']';
        },

        /**
         * Build a browsable tree with log data.
         */
        buildDataTree: function(obj) {
            // Prepare variables
            var self = this;
            var params = {
                product_id: obj.jsConfig.product.id,
                form_key: obj.jsConfig.product.form_key
            };

            // Set the data viewer button event
            $(this.getButtonSelector(obj)).on('click touch', function() {
                // Send the request
                AipSlider.showLoader(self);
                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: UrlBuilder.build(self.logsUrl),
                    data: params,
                    success: function (data) {
                        // Get the HTML content
                        AipModal.addHtml(
                            AipSlider.nextSlideSelector,
                            data.html
                        );

                        // Build the data tree
                        AipTree.build(obj);
                    },
                    error: function (request, status, error) {
                        self.log(
                            obj,
                            __('Error retrieving the UI logging data'),
                            error
                        );
                    }
                });
            });
        },

        /**
         * Get the target button for UI logging.
         */
        getButtonSelector: function(obj) {
            return '#' + this.logViewerButtonClass + '-' + obj.jsConfig.product.id;
        }
    };
});
