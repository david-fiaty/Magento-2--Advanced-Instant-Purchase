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

        /**
         * Build a jQtree instance.
         */
        build: function () {
            if (this.needsUiLogging()) {
                $(this.treeContainerSelector).jsonViewer(
                    this.o.config,
                    {
                        collapsed: true
                    }
                );
            }
        },

        /**
         * Check if UI logging i enabled.
         */
        needsUiLogging: function () {
            return this.o.config.general.debug_enabled
             && this.o.config.general.ui_logging_enabled;
        }
    };
});
