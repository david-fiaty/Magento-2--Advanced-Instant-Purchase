define([
    'mage/translate',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/tree'
], function (__, AipTree) {
    'use strict';

    return {
        logCount: 1,

        /**
         * Log data to the browser console.
         *
         * @param {Object} data
         */
        log: function(obj, msg, data) {
            // Default data value
            data = data || null;

            // Check the logging settings
            var condition = obj.jsConfig.general.debug_enabled
            && obj.jsConfig.general.console_logging_enabled;

            // Handle the logging display
            if (condition) {
                // Module name
                console.log(
                    '%c[' + this.logCount + '][' + obj.jsConfig.module.title + ']',
                    'font-weight: bold; color: blue;'
                );

                // Log event title
                console.log(msg)

                // Log event data
                if (data) { 
                    console.log(data);
                }

                // Log count
                this.logCount++;
            }
        },

        /**
         * Build a browsable tree with log data.
         *
         * @param {Object} data
         */
        buildDataTree: function(obj) {
            AipTree.build(obj);
        }
    };
});
