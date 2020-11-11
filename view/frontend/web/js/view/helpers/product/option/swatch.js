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
        swatchOptionSelectorPrefix: '.swatch-opt-',

        /**
         * Set product options events.
         */
        initOptionEvent: function (option) {
            // Prepare variables
            var self = this;
            var sourceField = this.getSourceField(option);

            // Set the value change events
            $(sourceField).off().on('click touch', function (e) {
                // Prepare the target Id
                var targetField = self.getTargetField(option);

                // Get the source value
                var sourceFieldValue = $(e.originalEvent.target).attr('option-id');

                // Assign value from source to target
                $(targetField).val(sourceFieldValue);
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
         * Get an option field selector.
         */
        getSourceField: function (option) {
            var output;
            if (NbnView.isListView()) {
                output = this.getSwatchValuesSelectors(option);
            }
            else if (NbnView.isPageView()) {
                output = this.swatchOptionSelector
                + ' '
                + this.swatchAttributeSelector
                + '[attribute-id="' + option['attribute_id'] + '"]';
            }

            return output;
        },

        /**
         * Get an option field value.
         */
        getSourceFieldValue: function (sourceField) {
            return NbnView.isListView()
            ? $(sourceField).val()
            : $(sourceField).find('.selected').attr('option-id');
        },
        
        /**
         * Get a target option hidden field selector.
         */
        getTargetField: function (option) {
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
         * Get a swatch option values selectors.
         */
        getSwatchValuesSelectors: function (option) {
            // Prepare the selector prefix
            var selectors = [];
            var selectorPrefix = this.swatchOptionSelectorPrefix
            + option['product_id']
            + ' '
            + '.swatch-option';

            // Add the swatch option values selectors
            for (var i = 0; i < option['values'].length; i++) {
                // Prepare the value selector
                var selector = selectorPrefix 
                + '[option-id="' 
                + option['values'][i]['value_index']+ '"]';

                // Add to the array
                selectors.push(selector);
            }

            return selectors.join(', ');
        },

        /**
         * Update the selected product options values.
         */
        updateSelectedOptionValue: function (option) {
            // Prepare the parameters
            var targetField = this.getTargetField(option);
            var sourceField = this.getSourceField(targetField);
            var sourceFieldValue = this.getSourceFieldValue(sourceField);

            if (typeof sourceFieldValue !== 'undefined') {
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
