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
         * Get a page swatch option values selectors.
         */
        getValuesSelectors: function (option) {
            // Prepare the selector prefix
            var selectors = [];
            var selectorPrefix = '.swatch-opt .swatch-attribute .swatch-option';

            // Add the swatch option values selectors
            for (var i = 0; i < option['values'].length; i++) {
                // Prepare the value selector
                var selector = selectorPrefix + '[option-id="' + option['values'][i]['value_index']+ '"]';

                // Add to the array
                selectors.push(selector);
            }

            return selectors.join(', ');
        },

        /**
         * Get an option field value.
         */
        getSourceFieldValue: function (sourceField) {
            return $(sourceField).find('.selected').attr('option-id');
        }
    };
});
