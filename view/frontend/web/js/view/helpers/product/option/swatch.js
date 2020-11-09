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
        swatchOptionSelectorPrefix: '.swatch-opt-',

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
                $(sourceField).on('click touch', function (e) {
                    // Prepare the target Id
                    var targetId = '#nbn-super-attribute-';
                    targetId += option['product_id'];
                    targetId += '-';
                    targetId += $(this).attr('attribute-id');

                    // Get the source value
                    var val = $(e.originalEvent.target).attr('option-id');

                    // Assign value from source to target
                    $(targetId).val(val);
                });
            }
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
            if (NbnView.isListView()) {
                return this.swatchOptionSelectorPrefix
                + option['product_id'] 
                + ' .swatch-attribute';
            }
            else if (NbnView.isPageView()) {
                return this.swatchOptionSelectorPrefix;
            }
        },

        /**
         * Update the selected product options values.
         */
        updateSelectedOptionsValues: function (obj) {
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
    };
});
