define([
    'mage/translate',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/modal',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/tree'
], function (__, AipModal, AipTree) {
    'use strict';

    return {
        logCount: 1,
        logTitleCss: 'font-weight: bold; color: blue;',
        logViewerButtonSelector: '.aip-ui-logger-button',
        logViewerBoxSelector: '.aip-ui-logger',

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
            // Build the data tree
            AipTree.build(obj);

            // Set the log viewwe button event
            $(this.logViewerButtonSelector).on('click touch', function () {
                $(this.logViewerBoxSelector).toggle();
            });
        }
    };
});
