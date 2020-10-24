define([
    'jquery',
    'jqtree'
], function ($, jqtree) {
    'use strict';

    return {
        treeContainerSelector: '.aip-ui-logger-tree',

        /**
         * Build a jQtree instance.
         */
        build: function(obj) {
            if (this.needsUiLogging(obj)) {
                $(this.treeContainerSelector).tree({
                    data:  JSON.parse(obj.jsConfig),
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
