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
        initAttributeEvent: function (attribute) {
            // Prepare variables
            var self = this;
            var sourceFieldId = this.getAttributeFieldId(attribute);

            // Set the value change events
            $(sourceFieldId).on('change', function (e) {
                // Prepare the target Id
                var targetFieldId = self.getHiddenFieldId(attribute);

                // Assign value from source to target
                $(targetFieldId).val($(e.currentTarget).val());
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
         * Check if a product attribute is valid.
         */
        isAttributeInvalid: function (attribute) {
            // Prepare the target Id
            var targetId = this.getTargetValidationField(attribute);

            // Get the field value
            var val = this.getSourceFieldValue(targetId);

            // Check the field value
            var isValid = val && val.length > 0 && parseInt(val) > 0;

            return !isValid;
        },

        /**
         * Get a source attribute field id.
         */
        getAttributeFieldId: function (attribute) {
            return this.attributeSelectorPrefix
                + attribute['product_id']
                + '-' + attribute['attribute_id'];
        },

        /**
         * Get an option field value.
         */
        getSourceFieldValue: function (sourceFieldId) {
            return $(sourceFieldId).val();
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

        /**
         * Get a target attribute hidden field selector.
         */
        getTargetValidationField: function (attribute) {
            return this.superAttributeSelectorPrefix
            + attribute['product_id']
            + '-'
            + attribute['attribute_id'];
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
