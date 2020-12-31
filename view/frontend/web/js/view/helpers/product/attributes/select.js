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
    'mage/translate'
], function ($, __) {
    'use strict';

    return {
        confirmationContainerSelector: '#nbn-confirmation-content',
        attributeSelectorPrefix: '#nbn-attribute-',
        superAttributeSelectorPrefix: '#nbn-super-attribute-',
        optionFieldSelector: '.nbn-popup-option',

        /**
         * Set a product attribute events.
         */
        initAttributeEvent: function (option) {
            // Prepare variables
            var self = this;
            var sourceFieldId = this.getAttributeFieldId(option);

            // Set the value change events
            $(sourceFieldId).on('change', function (e) {
                // Prepare the target Id
                var targetFieldId = self.getHiddenFieldId(option);

                // Assign value from source to target
                $(targetFieldId).val($(e.currentTarget).val());
            });
        },

        /**
         * Check if a product attributes are valid.
         */
        getAttributeErrors: function (option, e) {
            return this.isAttributeInvalid(e, option)
            ? [option]
            : [];
        },

        /**
         * Check if a product attribute is valid.
         */
        isAttributeInvalid: function (e, option) {
            // Prepare the target Id
            var targetId = this.getTargetValidationField(e, option);

            // Get the field value
            var val = this.getSourceFieldValue(targetId);

            // Check the field value
            var isValid = val && val.length > 0 && parseInt(val) > 0;

            return !isValid;
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
         * Get an option field value.
         */
        getSourceFieldValue: function (sourceFieldId) {
            return $(sourceFieldId).val();
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
         * Get a target option hidden field selector.
         */
        getTargetValidationField: function (e, option) {
            return this.superAttributeSelectorPrefix
            + $(e.currentTarget).data('product-id')
            + '-'
            + option['attribute_id'];
        },

        /**
         * Update the selected product attribute values.
         */
        updateSelectedAttributeValue: function (option) {
            // Prepare the parameters
            var sourceFieldId = this.getHiddenFieldId(option);
            var sourceFieldValue = $(sourceFieldId).val();
            var targetFieldId = this.getAttributeFieldId(option);

            // Update the option selected value
            if (this.isSelectedValueValid(sourceFieldValue)) {
                $(this.confirmationContainerSelector).find(targetFieldId).val(sourceFieldValue).change();
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
