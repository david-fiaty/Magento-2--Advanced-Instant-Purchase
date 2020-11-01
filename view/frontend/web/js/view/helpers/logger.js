define([], function() {
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
            console.log('|--' + msg)

            // Log event data
            if (data) {
                console.log(data);
            }

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
         * Get the target button for UI logging.
         */
        getButtonSelector: function(obj) {
            return '#' + this.logViewerButtonClass + '-' + obj.jsConfig.product.id;
        }
    };
});
