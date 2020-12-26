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
        selectorPrefix: '.swatch-opt .swatch-attribute .swatch-option',
        attributeSelectorPrefix: '#nbn-option-',

        /**
         * Get a page swatch option values selectors.
         */
        getValuesSelectors: function (option) {
            // Prepare the selector prefix
            var selectors = [];

            // Add the swatch option values selectors
            for (var i = 0; i < option['values'].length; i++) {
                selectors.push(this.getSourceFieldSelector(
                    option,
                    option['values'][i]['value_index']
                ));
            }

            return selectors.join(', ');
        },

        /**
         * Get a source option field id.
         */
        getAttributeFieldId: function (option) {
            return this.attributeSelectorPrefix
                + option['product_id']
                + '-' + option['attribute_id'];
        },

        /**
         * Get a source field selector.
         */
        getSourceFieldSelector: function (option, valueIndex) {
            return this.selectorPrefix + '[attribute-id="' + valueIndex + '"]';
        },

        /**
         * Get an option field value.
         */
        getSourceFieldValue: function (sourceFieldId) {
            return $(sourceFieldId).find('.selected').attr('attribute-id');
        }
    };
});
