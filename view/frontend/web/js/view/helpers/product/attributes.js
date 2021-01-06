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
], function ($) {
    'use strict';

    return {
        /**
         * Initialise the product attributes fields events.
         */
        initFields: function (attribute) {
            // Set the attribute value change events
            $(this.getSwatchAttributesSelectors(attribute)).on('click touch', {attribute: attribute}, function (e) {
                // Build the hidden field selector
                var hiddenField = '#nbn-super-attribute-'
                + e.data.attribute.product_id
                + '-' + e.data.attribute.attribute_id;

                // Assign the attribute value to the hidden field
                $(hiddenField).val($(e.currentTarget).attr('option-id'));
            });
        },

        /**
         * Get a product swatch attributes selectors.
         */
        getSwatchAttributesSelectors: function (attribute) {
            var selectors = [];
            for (var i = 0; i < attribute.values.length; i++) {
                // Build the selector
                var swatchValueSelector = '.swatch-opt-' 
                + attribute.product_id + ' .swatch-option'
                + '[option-id="' + attribute.values[i].value_index + '"]'; 

                // Add to the list
                selectors.push(swatchValueSelector);
            }

            return selectors.join(', ');
        }
    }
});


