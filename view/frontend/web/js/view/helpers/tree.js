define([
    'jquery',
    'jsonViewer'
], function($, jsonViewer) {
    'use strict';

    return {
        treeContainerSelector: '.aip-logger-tree',

        /**
         * Build a jQtree instance.
         */
        build: function() {
            if (this.needsUiLogging()) {
                // Initialize the data tree viewer
                $(this.treeContainerSelector).jsonViewer(
                    this.o.jsConfig,
                    {
                        collapsed: false
                    }
                );
            }
        },

        /**
         * Check if UI logging i enabled.
         */
        needsUiLogging: function() {
            return this.o.jsConfig.general.debug_enabled
             && this.o.jsConfig.general.ui_logging_enabled;
        }
    };
});
