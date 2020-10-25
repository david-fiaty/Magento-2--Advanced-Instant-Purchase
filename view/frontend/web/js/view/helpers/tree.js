define([
    'jquery',
    'jsonViewer'
], function ($, jsonViewer) {
    'use strict';

    return {
        treeContainerSelector: '.aip-ui-logger-tree',

        /**
         * Build a jQtree instance.
         */
        build: function(obj) {
            var test = [
                {
                    name: 'node1',
                    children: [
                        { name: 'child1' },
                        { name: 'child2' }
                    ]
                },
                {
                    name: 'node2',
                    children: [
                        { name: 'child3' }
                    ]
                }
            ];

            var data = [obj.jsConfig];

            if (this.needsUiLogging(obj)) {
                $(this.treeContainerSelector).jsonViewer(
                    JSON.stringify(obj.jsConfig)
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
