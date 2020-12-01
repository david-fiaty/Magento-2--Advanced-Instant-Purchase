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
         * Get a card option public hash.
         */
        getOptionPublicHash: function (val) {
            return val.split('*~*')[0];
        },

        /**
         * Format a card icon.
         */
        formatIcon: function (state) {
            // Check the element state
            if (!state.id || !state.element.parentElement.className.includes('nbn-payment-method-select')) {
                return state.text;
            }

            // Get the icon URL
            var iconUrl = state.element.value.split('*~*')[1];

            // Build the icon HTML
            var iconHtml = $(
                '<span class="nbn-card-icon">'
                + '<img src="' + iconUrl + '">'
                + state.text + '</span>'
            );

            return iconHtml;
        },

        /**
         * Check if an object has a property.
         */
        has: function (target, path, value) {
            if (typeof target !== 'object' || target === null) {
                return false; }
                var parts = path.split('.');
            while (parts.length) {
                var property = parts.shift();
                if (!(target.hasOwnProperty(property))) {
                    return false;
                }
                target = target[property];
            }
            if (value) {
                return target === value;
            } else {
                return true;
            }
        }
    }
});
