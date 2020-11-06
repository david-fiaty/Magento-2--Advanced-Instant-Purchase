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
    'Naxero_BuyNow/js/view/helpers/logger',
    'Naxero_BuyNow/js/view/helpers/view',
    'popover',
], function ($, __, NbnLogger, NbnView, popover) {
    'use strict';

    return {
        listProductContainerSelector: '.product-item',
        listProductFormSelector: '.nbn-list-form',
        listProductCartFormSelector: 'form[data-role="tocart-form"]',
        viewProductContainerSelector: '.product-info-main',
        viewProductFormSelector: '#product_addtocart_form',
        productBoxContainerSelector: '.nbn-product-box-container',
        confirmationContainerSelector: '#nbn-confirmation-content',
        optionFieldSelector: '#nbn-option',
        optionSelectorPrefix: '#nbn-option-',
        popoverSelector: '.popover',
        productDataSelectorPrefix: '#nbn-product-data-',
        buttonErrorClass: 'nbn-button-error',

        /**
         * Initialise the object.
         */
        init: function (obj) {
            this.o = obj;
            return this;
        },

        /**
         * Get a product container selector.
         */
        getProductContainer: function () {
            return NbnView.isListView()
            ? this.listProductContainerSelector
            : this.viewProductContainerSelector;
        },

        /**
         * Get a product container selector.
         */
        getProductForm: function () {
            // Product container selector
            var productContainerSelector = this.getProductContainer();

            // Get product form selector
            var productFormSelector = NbnView.isListView()
            ? this.listProductFormSelector
            : this.viewProductFormSelector;

            // Get the form
            var form = $(this.o.jsConfig.product.button_selector).closest(productContainerSelector)
            .find(productFormSelector);

            return form;
        },

        /**
         * Get the product form data.
         */
        getProductFormData: function () {
            // Product container selector
            var productContainerSelector = this.getProductContainer();

            // Get the buy now data
            var buyNowData = this.getProductForm().serialize();

            // Log the purchase data
            NbnLogger.log(
                __('Place order form data'),
                this.getProductForm().serializeArray()
            );

            // Get the cart form data if list view
            if (NbnView.isListView()) {
                var cartFormData = $(this.o.jsConfig.product.button_selector)
                .closest(productContainerSelector)
                .find(this.listProductCartFormSelector)
                .serialize();

                // Add the cart form data to the purchase data
                buyNowData += '&' + cartFormData;
            }

            return buyNowData;
        },

        /**
         * Set product options events.
         */
        initOptionsEvents: function () {
            if (this.hasOptions()) {
                // Prepare the variables
                var options = this.o.jsConfig.product.options;

                // Set the options events and default values
                for (var i = 0; i < options.length; i++) {
                    // Prepare the fields
                    var option = options[i];
                    var sourceField = this.getOptionField(option);

                    // Set the value change events
                    $(sourceField).on('change', function (e) {
                        var sourceId = e.currentTarget;
                        var targetId = 'input[name="super_attribute[' + $(this).data('attribute-id') + ']"]';
                        $(targetId).val($(sourceId).val());
                    });
                }
            }
        },

        /**
         * Product options validation.
         */
        validateOptions: function (e) {
            if (this.hasOptions(e)) {
                return this.getOptionsErrors().length == 0;
            }

            return true;
        },

        /**
         * Check if a product has options.
         */
        hasOptions: function (e) {
            e = e || null;
            var product;
            if (e) {
                product = this.getProductData(e);
            }
            else {
                product = this.o.jsConfig.product;
            }

            return product.options.length
            && product.options.length > 0;
        },

        /**
         * Get updated product data for events.
         */
        getProductData: function (e) {
            var productId = $(e.currentTarget).data('product-id');
            var productData = $(this.productDataSelectorPrefix + productId).val();
            
            return JSON.parse(productData);
        },

        /**
         * Check if a product options are valid.
         */
        getOptionsErrors: function () {
            // Prepare variables
            var options = this.o.jsConfig.product.options;
            var errors = [];

            // Check each option
            for (var i = 0; i < options.length; i++) {
                if (this.isOptionInvalid(options[i])) {
                    errors.push(options[i]);
                }
            }

            return errors;
        },

        /**
         * Check if a product option is valid.
         */
        isOptionInvalid: function (option) {
            // Find the target field
            var targetField = 'input[name="super_attribute[' + option['attribute_id'] + ']"';

            // Check the value
            var val = $(targetField).val();
            var isValid = val && val.length > 0 && parseInt(val) > 0;

            return !isValid;
        },

        /**
         * Get an option field selector.
         */
        getOptionField: function (option) {
            // Todo - Handle list view case with swatch options or not
            return this.optionSelectorPrefix
            + this.o.jsConfig.product.id
            + '-' + option['attribute_id'];
        },

        /**
         * Update the selected product options values.
         */
        updateSelectedOptionsValues: function () {
            if (this.hasOptions() && this.o.jsConfig.blocks.show_product) {
                var options = this.o.jsConfig.product.options;
                for (var i = 0; i < options.length; i++) {
                    // Prepare the parameters
                    var sourceField = 'input[name="super_attribute[' + options[i]['attribute_id'] + ']"]';
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
        },

        /**
         * Display the product options errors.
         */
        displayErrors: function (e) {
            // Prepare variables
            var self = this;
            var button = $(e.currentTarget);

            // Clear previous errors
            self.clearErrors(e);

            // Update the button state
            button.popover({
                title : '',
                content : __('Please select options for this product'),
                autoPlace : false,
                trigger : 'hover',
                placement : 'right',
                delay : 10
            });
            button.addClass(this.buttonErrorClass);
            button.trigger('mouseover');
        },

        /**
         * Clear UI error messages.
         */
        clearErrors: function (e) {
            $(e.currentTarget).removeClass(this.buttonErrorClass);
            $(this.popoverSelector).remove();
        }
    };
});
