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
    'mage/translate',
    'Naxero_BuyNow/js/view/helpers/view',
    'Naxero_BuyNow/js/view/helpers/product/attributes/swatch/list',
    'Naxero_BuyNow/js/view/helpers/product/attributes/swatch/page'
], function ($, __, NbnView, NbnListSwatch, NbnPageSwatch) {
    'use strict';

    return {
        confirmationContainerSelector: '#nbn-confirmation-content',
        attributeSelectorPrefix: '#nbn-attribute-',
        superAttributeSelectorPrefix: '#nbn-super-attribute-',

        /**
         * Set product options events.
         */
        initAttributeEvent: function (attribute) {
            // Prepare variables
            var self = this;
            var sourceFields = this.getValuesSelectors(attribute);

            // Set the value change events
            $(sourceFields).on('click touch', function (e) {
                // Prepare the target Id
                var targetFieldId = self.getHiddenFieldId(attribute);

                // Get the source value
                var sourceFieldValue = $(e.originalEvent.target).attr('attribute-id');

                // Assign value from source to target
                $(targetFieldId).val(sourceFieldValue);
            });
        },

        /**
         * Check if a product attributes are valid.
         */
        getAttributeErrors: function (attribute) {
            return this.isAttributeInvalid(attribute)
            ? [attribute]
            : [];
        },

        /**
         * Update the selected product attribute value.
         */
        updateSelectedAttributeValue: function (attribute) {
            // Prepare the parameters
            var sourceFieldId = this.getHiddenFieldId(attribute);
            var sourceFieldValue = $(sourceFieldId).val();
            var targetFieldId = this.getAttributeFieldId(attribute);

            // Update the option selected value
            if (this.isSelectedValueValid(sourceFieldValue)) {
                $(this.confirmationContainerSelector)
                .find(targetFieldId)
                .val(sourceFieldValue)
                .change();
            }
        },

        /**
         * Check if a product attribute is valid.
         */
        isAttributeInvalid: function (attribute) {
            // Prepare the target Id
            var targetId = this.getHiddenFieldId(attribute);

            // Get the field value
            var val = $(targetId).val();

            // Check the field value
            var isValid = val && val.length > 0 && parseInt(val) > 0;

            return !isValid;
        },

        /**
         * Get a source attribute field id.
         */
        getAttributeFieldId: function (attribute) {
            return this.getSwatchHandler().getAttributeFieldId(attribute);
        },

        /**
         * Get the swatch attributes handler.
         */
        getSwatchHandler: function () {
            if (NbnView.isListView()) {
                return NbnListSwatch;
            } else if (NbnView.isPageView()) {
                return NbnPageSwatch;
            }

            return;
        },

        /**
         * Get an attribute field values selectors.
         */
        getValuesSelectors: function (attribute) {
            return this.getSwatchHandler().getValuesSelectors(attribute);
        },

        /**
         * Get a target attribute hidden field selector.
         */
        getHiddenFieldId: function (attribute) {
            return this.superAttributeSelectorPrefix
            + attribute['product_id']
            + '-'
            + attribute['attribute_id'];
        },

        isSelectedValueValid: function (value) {
            return value
            && typeof value !== 'undefined'
            && value != 'undefined'
            && value.length > 0;
        }
    };
});
