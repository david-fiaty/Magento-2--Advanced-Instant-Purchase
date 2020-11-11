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
        optionSelectorPrefix: '#nbn-option-',
        superAttributeSelectorPrefix: '#nbn-super-attribute-',

        /**
         * Set product options events.
         */
        initOptionEvent: function (option) {
            // Prepare variables
            var self = this;
            var sourceFields = this.getValuesSelectors(option);

            // Set the value change events
            $(sourceFields).off().on('click touch', function (e) {
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
            var targetId = this.getTargetField(option);

            // Get the field value
            var val = this.getSwatchHandler().getSourceFieldValue(targetId);

            console.log('isOptionInvalid');
            console.log(targetId);
            console.log(val);

            // Check the field value
            var isValid = val && val.length > 0 && parseInt(val) > 0;

            return !isValid;
        },

        /**
         * Get the swatch options handler.
         */
        getSwatchHandler: function () {
            if (NbnView.isListView()) {
                return NbnListSwatch;
            }
            else if (NbnView.isPageView()) {
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
        getTargetField: function (option) {
            return this.superAttributeSelectorPrefix
            + option['product_id']
            + '-'
            + option['attribute_id'];
        },

        /**
         * Update the selected product options values.
         */
        updateSelectedOptionValue: function (option) {
            // Prepare the parameters
            var targetField = this.getTargetField(option);
            var sourceFieldValue = this.getSwatchHandler().getSourceFieldValue(targetField);

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
