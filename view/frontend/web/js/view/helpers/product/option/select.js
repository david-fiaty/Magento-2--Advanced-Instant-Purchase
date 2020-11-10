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
    'Naxero_BuyNow/js/view/helpers/view'
], function ($, __, NbnView) {
    'use strict';

    return {
        confirmationContainerSelector: '#nbn-confirmation-content',
        optionSelectorPrefix: '#nbn-option-',
        superAttributeSelectorPrefix: '#nbn-super-attribute-',

        /**
         * Set product options events.
         */
        initOptionEvent: function (option) {
            // Prepare variables
            var self = this;
            var sourceField = this.getSourceField(option);

            // Set the value change events
            $(sourceField).on('change', function (e) {
                // Prepare the target Id
                var targetId = self.getTargetField($(this));

                // Assign value from source to target
                $(targetId).val($(e.currentTarget).val());
            });
        },

        /**
         * Check if a product options are valid.
         */
        getOptionsErrors: function (options, e) {
            // Prepare variables
            var errors = [];

            // Check each option
            for (var i = 0; i < options.length; i++) {
                if (this.isOptionInvalid(e, options[i])) {
                    errors.push(options[i]);
                }
            }

            return errors;
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
         * Get a source option field selector.
         */
        getSourceField: function (option) {
            return this.optionSelectorPrefix
                + option['product_id']
                + '-' + option['attribute_id'];
        },

        /**
         * Get an option field value.
         */
        getSourceFieldValue: function (sourceField) {
            return $(sourceField).val();
        },

        /**
         * Get a target option hidden field selector.
         */
        getTargetField: function (sourceField) {
            return this.superAttributeSelectorPrefix
            + $(sourceField).data('product-id')
            + '-'
            + $(sourceField).data('attribute-id');
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
            for (var i = 0; i < options.length; i++) {
                // Prepare the parameters
                var targetField = this.getTargetField(options[i]);
                var sourceField = this.getSourceField(targetField);
                var sourceFieldValue = this.getSourceFieldValue(sourceField);

                // Prepare the conditions
                var condition = sourceFieldValue
                && sourceFieldValue != 'undefined'
                && sourceFieldValue.length > 0;

                // Update the options selected value
                if (condition) {
                    $(this.confirmationContainerSelector).find(targetField).val(sourceFieldValue).change();
                }
            }
        }
    };
});
