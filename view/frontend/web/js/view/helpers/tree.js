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
        build: function () {
            if (this.needsUiLogging()) {
                console.log('ccc');
                console.log(window.naxero.nbn[1218]);
                console.log(JSON.stringify(window.naxero.nbn[1218]));
                $(this.treeContainerSelector).jsonViewer(
                    JSON.stringify(window.naxero.nbn[1218]),
                    {collapsed: true}
                );
            }
        },

        /**
         * Check if UI logging i enabled.
         */
        needsUiLogging: function () {
            return window.naxero.nbn.current.general.debug_enabled
             && window.naxero.nbn.current.general.ui_logging_enabled;
        }
    };
});
