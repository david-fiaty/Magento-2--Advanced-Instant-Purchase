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

        /**
         * Set product options events.
         */
        initOptionsEvents: function (options) {
            // Set the options events and default values
            for (var i = 0; i < options.length; i++) {
                // Prepare the fields
                var option = options[i];
                var sourceField = this.getOptionField(option);

                // Set the value change events
                $(sourceField).on('change', function (e) {
                    // Prepare the target Id
                    var targetId = '#nbn-super-attribute-';
                    targetId += $(this).data('product-id');
                    targetId += '-';
                    targetId += $(this).data('attribute-id');

                    // Assign value from source to target
                    $(targetId).val($(e.currentTarget).val());
                });
            }
        },

        /**
         * Product options validation.
         */
        validateOptions: function (e) {
            if (this.hasOptions(e)) {
                return this.getOptionsErrors(e).length == 0;
            }

            return true;
        },

        /**
         * Check if a product has options.
         */
        hasOptions: function (e) {
            var product = this.getProductData(e);

            return product.options.length > 0;
        },

        /**
         * Get a product options.
         */
        getOptions: function (e) {
            return this.getProductData(e);
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
            var targetId = '#nbn-super-attribute-';
            targetId += $(e.currentTarget).data('product-id');
            targetId += '-';
            targetId += option['option_id'];

            // Get the field value
            var val = $(targetId).val();

            // Check the field value
            var isValid = val && val.length > 0 && parseInt(val) > 0;

            return !isValid;
        },

        /**
         * Get an option field selector.
         */
        getOptionField: function (option) {
            return this.optionSelectorPrefix
                + option['product_id']
                + '-' + option['attribute_id'];
        },

        /**
         * Update the selected product options values.
         */
        updateSelectedOptionsValues: function (obj) {
            if (this.hasOptions() && obj.jsConfig.blocks.show_product) {
                var options = obj.jsConfig.product.options;
                for (var i = 0; i < options.length; i++) {
                    // Prepare the parameters
                    var sourceField = '#nbn-super-attribute-' + options[i]['product_id'] + '-' + options[i]['attribute_id'];
                    var targetField = this.getOptionField(options[i]);
                    var sourceFieldValue = $(sourceField).val();

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
        }
    };
});
