define([
    'jquery',
    'mage/translate',
    'mage/url',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/validation',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/logger',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/view'
], function($, __, UrlBuilder, AipValidation, AipLogger, AipView) {
    'use strict';

    return {
        listProductContainerSelector: '.product-item',
        listProductFormSelector: '.aip-list-form',
        listProductCartFormSelector: 'form[data-role="tocart-form"]',
        viewProductContainerSelector: '.product-info-main',
        viewProductFormSelector: '#product_addtocart_form',
        productDataUrl: 'naxero-aip/ajax/product',
        productBoxContainerSelector: '.aip-product-box-container',
        optionFieldSelector: '#aip-option',

        /**
         * Get a product container selector.
         */
        getProductContainer: function(obj) {
            return AipView.isListView(obj)
            ? this.listProductContainerSelector
            : this.viewProductContainerSelector;
        },

        /**
         * Get a product container selector.
         */
        getProductForm: function(obj) {
            // Product container selector
            var productContainerSelector = this.getProductContainer(obj);

            // Get product form selector
            var productFormSelector = AipView.isListView(obj)
            ? this.listProductFormSelector
            : this.viewProductFormSelector;

            // Get the form
            var form = $(obj.getButtonId()).closest(productContainerSelector)
            .find(productFormSelector);

            return form;
        },

        /**
         * Get the product form data.
         */
        getProductFormData: function(obj) {
            // Product container selector
            var productContainerSelector = this.getProductContainer(obj);

            // Get the buy now data
            var buyNowData = this.getProductForm(obj).serialize();

            // Log the purchase data
            AipLogger.log(
                obj,
                __('Place order purchase data'),
                this.getProductForm(obj).serializeArray()
            );

            // Get the cart form data if list view
            if (AipView.isListView(obj)) {
                var cartFormData = $(obj.getButtonId())
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
        initOptionsEvents: function(obj) {
            if (this.hasOptions(obj)) {
                var options = obj.jsConfig.product.options;
                for (var i = 0; i < options.length; i++) {
                    // Prepare the fields
                    var sourceField = this.getOptionField(obj, options[i]);
                    var targetField = this.getOptionHiddenField(obj, options[i]);

console.log(sourceField);
console.log(targetField);
console.log(obj.jsConfig.product.button_container_selector);

                    // Set the value change event
                    $(sourceField).on('change', function() {
                        $(obj.jsConfig.product.button_container_selector).find(targetField).val($(sourceField).val());
                    });
                }
            }
        },

        /**
         * Product options validation.
         */
        validateOptions: function(obj) {
            if (this.hasOptions(obj)) {
                return this.getOptionsErrors(obj).length == 0;
            }

            return true;
        },

        /**
         * Check if a product has options.
         */
        hasOptions: function(obj) {
            return obj.jsConfig.product.options.length
            && obj.jsConfig.product.options.length > 0;
        },

        /**
         * Check if a product options are valid.
         */
        getOptionsErrors: function(obj) {
            // Prepare variables
            var options = obj.jsConfig.product.options;
            var errors = [];

            // Check each option
            for (var i = 0; i < options.length; i++) {
                if (this.isOptionInvalid(obj, options[i])) {
                    errors.push(options[i]);
                }
            }

            // Display the errors
            AipValidation.clearErrors(obj);
            if (errors.length > 0) AipValidation.displayOptionsError(obj);

            return errors;
        },

        /**
         * Check if a product option is valid.
         */
        isOptionInvalid: function(obj, option) {
            // Find the target field
            var targetField = this.getOptionHiddenField(obj, option);

            // Check the value
            var val = $(targetField).val();
            var isValid = val && val.length > 0 && parseInt(val) > 0;

            return !isValid;
        },

        /**
         * Get an option field selector.
         */
        getOptionField: function(obj, option) {
            // Product container selector
            var productContainerSelector = this.getProductContainer(obj);

            // Option selector
            var optionSelector = '#aip-option-' + option['attribute_id'];
            
            return $(productContainerSelector).find(optionSelector).attr('id');
        },

        /**
         * Get an option hidden field selector.
         */
        getOptionHiddenField: function(obj, option) {
            // Input field
            var inputFieldSelector = 'input[type="hidden"][id="super_attribute_' + option['attribute_id'] + '"]';
            var inputField = $(obj.jsConfig.product.button_container_selector).find(inputFieldSelector);

            return '#' + inputField.attr('id');
        },

        /**
         * Render a product box.
         */
        renderBox: function(obj) {
            // Prepare the parameters
            var self = this;
            var params = {
                product_id: obj.jsConfig.product.id,
                form_key: obj.jsConfig.product.form_key
            };

            // Send the AJAX request
            $.ajax({
                type: 'POST',
                url: UrlBuilder.build(self.productDataUrl),
                data: params,
                success: function(data) {
                    // Get the HTML content
                    $(self.productBoxContainerSelector).html(data.html);
                },
                error: function(request, status, error) {
                    AipLogger.log(
                        self,
                        __('Error retrieving the confimation window product box'),
                        error
                    );
                }
            });
        }
    };
});
