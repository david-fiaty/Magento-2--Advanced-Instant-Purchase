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
    'jsonViewer'
], function ($, jsonViewer) {
    'use strict';

    return {
        treeContainerSelector: '.nbn-logger-tree',

        /**
         * Initialise the object.
         */
        init: function (obj) {
            this.o = obj;
            return this;
        },

        /**
         * Build a jQtree instance.
         */
        build: function () {
            if (this.needsUiLogging()) {
                $(this.treeContainerSelector).jsonViewer(
                    this.o.jsConfig,
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
            return this.o.jsConfig.general.debug_enabled
             && this.o.jsConfig.general.ui_logging_enabled;
        }
    };
});
