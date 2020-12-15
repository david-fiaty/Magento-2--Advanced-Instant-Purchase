/**
 * Naxero.com
 * Professional ecommerce integrations for Magento.
 *
 * PHP version 7
 *
 * @category  Magento2
 * @package   Naxero
 * @author    Platforms Development Team <contact@naxero.com>
 * @copyright © Naxero.com all rights reserved
 * @license   https://opensource.org/licenses/mit-license.html MIT License
 * @link      https://www.naxero.com
 */

 define([
    'jquery',
    'jsonviewer'
], function ($, jsonViewer) {
    'use strict';

    return {
        treeContainerSelector: '.nbn-logger-tree',
        treeDataSelector: '.nbn-ui-logger-data',

        /**
         * Build a jQtree instance.
         */
        build: function (productId) {
            if (this.needsUiLogging(productId)) {
                $(this.treeContainerSelector).jsonViewer(
                    window.naxero.nbn[productId],
                    {
                        collapsed: true,
                        withLinks: false
                    }
                );
            }
        },

        /**
         * Check if UI logging i enabled.
         */
        needsUiLogging: function (productId) {
            return window.naxero.nbn.instances[productId].debug.debug_enabled
             && window.naxero.nbn.instances[productId].debug.ui_logging_enabled;
        }
    };
});
