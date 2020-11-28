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
    'select2'
], function ($, __, select2) {
    'use strict';

    return {
        confirmationContainerSelector: '#nbn-confirmation-content',
        optionSelectorPrefix: '#nbn-option-',
        superAttributeSelectorPrefix: '#nbn-super-attribute-',
        selectFieldSelector: '.nbn-field select',

        /**
         * Set product options events.
         */
        initOptionEvent: function (option) {
            // Prepare variables
            var self = this;
            var sourceFieldId = this.getOptionFieldId(option);

            // Select 2 
            $(this.selectFieldSelector).select2 ({
                placeholder: __('Select an option'),
                minimumResultsForSearch: -1,
                theme: 'classic'
            });

            // Set the value change events
            $(sourceFieldId).on('change', function (e) {
                // Prepare the target Id
                var targetFieldId = self.getHiddenFieldId(option);

                // Assign value from source to target
                $(targetFieldId).val($(e.currentTarget).val());
            });
        },

        /**
         * Check if a product options are valid.
         */
        getOptionErrors: function (option, e) {
            return this.isOptionInvalid(e, option)
            ? [option]
            : [];
        },

        /**
         * Check if a product option is valid.
         */
        isOptionInvalid: function (e, option) {
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
        getOptionFieldId: function (option) {
            return this.optionSelectorPrefix
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
            + option['option_id'];
        },

        /**
         * Update the selected product options values.
         */
        updateSelectedOptionValue: function (option) {
            // Prepare the parameters
            var sourceFieldId = this.getHiddenFieldId(option);
            var sourceFieldValue = $(sourceFieldId).val();
            var targetFieldId = this.getOptionFieldId(option);

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
