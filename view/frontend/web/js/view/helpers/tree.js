define([
    'jquery',
    'jsonViewer'
], function ($, jsonViewer) {
    'use strict';

    return {
        treeContainerSelector: '.aip-logger-tree',

        /**
         * Build a jQtree instance.
         */
        build: function(obj) {
            if (this.needsUiLogging(obj)) {
                // Initialize the data tree viewer
                $(this.treeContainerSelector).jsonViewer(
                    obj.jsConfig,
                    {
                        collapsed: false
                    }
                );                
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
