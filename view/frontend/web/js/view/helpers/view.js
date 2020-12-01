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
    'jquery'
], function ($) {
    'use strict';

    return {

        /**
         * Check if the current product is in list view.
         */
        isListView: function () {
            return this.o.config.product.display == 'list';
        },

        /**
         * Check if the current product is in block view.
         */
        isWidgetView: function () {
            return this.o.config.product.display == 'widget';
        },

        /**
         * Check if the current product is in page view.
         */
        isPageView: function () {
            return !this.isWidgetView() && !this.isListView();
        },

        /**
         * Check if the current product has options.
         */
        hasOptions: function () {
            return this.o.config.product.has_options;
        }
    };

});
