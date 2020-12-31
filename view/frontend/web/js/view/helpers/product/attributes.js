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
    'Naxero_BuyNow/js/view/helpers/view',
    'Naxero_BuyNow/js/view/helpers/product/attributes/select',
    'Naxero_BuyNow/js/view/helpers/product/attributes/swatch'
], function ($, NbnView, NbnProductAttributeSelect, NbnProductAttributeSwatch) {
    'use strict';

    return {
        /**
         * Set product attributes events.
         */
        initAttributesEvents: function (productId) {
            var attributes = this.getAttributes(productId);
            if (attributes && attributes.length > 0) {
                for (var i = 0; i < attributes.length; i++) {
                    this.getAttributeHandler(attributes[i]['attribute_type']).initAttributeEvent(attributes[i]);
                }
            }
        },

        /**
         * Get a product attributes.
         */
        getAttributes: function (productId) {
            return window.naxero.nbn.instances[productId].product.attributes;
        },

        /**
         * Get the attribute handler component.
         */
        getAttributeHandler: function (attributeType) {
            // Argument provided
            attributeType = attributeType || null;
            if (attributeType == 'select') {
               return NbnProductAttributeSelect;
            }
            else if (attributeType == 'swatch') {
                return NbnProductAttributeSwatch;
            }
            else {
                if (NbnView.isPageView()) return NbnProductAttributeSwatch;
                if (NbnView.isListView()) return NbnProductAttributeSwatch;
                if (NbnView.isWidgetView()) return NbnProductAttributeSelect;
            }
        },

        /**
         * Product attributes validation.
         */
        validateAttributes: function (productId) {
            // Prepare variables
            var attributes = this.getAttributes(productId);

            console.log('attributes');
            console.log(productId);
            console.log(attributes);

            var condition1 = attributes && attributes.length > 0;
            var errors = 0;

            // Loop through the product attributes
            if (condition1) {
                for (var i = 0; i < attributes.length; i++) {
                    // Validate the attribute
                    var error = this.getAttributeHandler(attributes[i]['attribute_type'])
                    .getAttributeErrors(attributes[i], e)
                    .length > 0;

                    // Register the error
                    if (error) {
                        errors++;
                    }
                }

                return errors == 0;
            }

            return true;
        }
    }
});
