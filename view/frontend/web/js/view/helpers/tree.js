define([
    'jquery',
    'jqtree'
], function ($, jqtree) {
    'use strict';

    return {
        /**
         * Build a jQtree instance.
         */
        build: function(obj) {
            if (this.needsUiLogging(obj)) {
                $('.aip-ui-logger-tree').tree({
                    data: obj.jsCOnfig,
                    autoOpen: true
                });
            }
        },

        /**
         * Check if UI logging i enabled.
         */
        needsUiLogging: function(obj) {
            return obj.jsConfig.general.debug_enabled
             && obj.jsConfig.general.ui_logging_enabled;
        }
    };
});
