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
    'Naxero_BuyNow/js/view/helpers/product/option/swatch/list',
    'Naxero_BuyNow/js/view/helpers/product/option/swatch/page'
], function ($, __, NbnView, NbnListSwatch, NbnPageSwatch) {
    'use strict';

    return {
        confirmationContainerSelector: '#nbn-confirmation-content',
        attributeSelectorPrefix: '#nbn-attribute-',
        superAttributeSelectorPrefix: '#nbn-super-attribute-',

        /**
         * Set product options events.
         */
        initAttributeEvent: function (option) {
            // Prepare variables
            var self = this;
            var sourceFields = this.getValuesSelectors(option);

            // Set the value change events
            $(sourceFields).on('click touch', function (e) {
                // Prepare the target Id
                var targetFieldId = self.getHiddenFieldId(option);

                // Get the source value
                var sourceFieldValue = $(e.originalEvent.target).attr('attribute-id');

                // Assign value from source to target
                $(targetFieldId).val(sourceFieldValue);
            });
        },

        /**
         * Check if a product options are valid.
         */
        getAttributeErrors: function (option, e) {
            return this.isOptionInvalid(option, e)
            ? [option]
            : [];
        },

        /**
         * Check if a product option is valid.
         */
        isOptionInvalid: function (option, e) {
            // Prepare the target Id
            var targetId = this.getHiddenFieldId(option);

            // Get the field value
            var val = $(targetId).val();

            // Check the field value
            var isValid = val && val.length > 0 && parseInt(val) > 0;

            return !isValid;
        },

        /**
         * Get a source option field id.
         */
        getAttributeFieldId: function (option) {
            return this.getSwatchHandler().getAttributeFieldId(option);
        },

        /**
         * Get the swatch options handler.
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
         * Get an option field values selectors.
         */
        getValuesSelectors: function (option) {
            return this.getSwatchHandler().getValuesSelectors(option);
        },

        /**
         * Get a target option hidden field selector.
         */
        getHiddenFieldId: function (option) {
            return this.superAttributeSelectorPrefix
            + option['product_id']
            + '-'
            + option['attribute_id'];
        },

        /**
         * Update the selected product attribute value.
         */
        updateSelectedAttributeValue: function (option) {
            // Prepare the parameters
            var sourceFieldId = this.getHiddenFieldId(option);
            var sourceFieldValue = $(sourceFieldId).val();
            var targetFieldId = this.getAttributeFieldId(option);

            // Update the option selected value
            if (this.isSelectedValueValid(sourceFieldValue)) {
                $(this.confirmationContainerSelector)
                .find(targetFieldId)
                .val(sourceFieldValue)
                .change();
            }
        },

        isSelectedValueValid: function (value) {
            return value
            && typeof value !== 'undefined'
            && value != 'undefined'
            && value.length > 0;
        }
    };
});
