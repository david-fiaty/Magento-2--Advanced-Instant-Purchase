define([
    'jquery',
    'mage/translate',
    'mage/url',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/logger',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/view',
    'popover',
], function($, __, UrlBuilder, AipLogger, AipView, popover) {
    'use strict';

    return {
        listProductContainerSelector: '.product-item',
        listProductFormSelector: '.aip-list-form',
        listProductCartFormSelector: 'form[data-role="tocart-form"]',
        viewProductContainerSelector: '.product-info-main',
        viewProductFormSelector: '#product_addtocart_form',
        productDataUrl: 'naxero-aip/ajax/product',
        productBoxContainerSelector: '.aip-product-box-container',
        confirmationContainerSelector: '#aip-confirmation-content',
        optionFieldSelector: '#aip-option',
        optionSelectorPrefix: '#aip-option-',
        popoverSelector: '.popover',
        buttonErrorClass: 'aip-button-error',

        /**
         * Initialise the object.
         */
        init: function(obj) {
            this.o = obj;
            return this;
        },

        /**
         * Get a product container selector.
         */
        getProductContainer: function() {
            return AipView.isListView()
            ? this.listProductContainerSelector
            : this.viewProductContainerSelector;
        },

        /**
         * Get a product container selector.
         */
        getProductForm: function() {
            // Product container selector
            var productContainerSelector = this.getProductContainer();

            // Get product form selector
            var productFormSelector = AipView.isListView()
            ? this.listProductFormSelector
            : this.viewProductFormSelector;

            // Get the form
            var form = $(this.o.getButtonId()).closest(productContainerSelector)
            .find(productFormSelector);

            return form;
        },

        /**
         * Get the product form data.
         */
        getProductFormData: function() {
            // Product container selector
            var productContainerSelector = this.getProductContainer();

            // Get the buy now data
            var buyNowData = this.getProductForm().serialize();

            // Log the purchase data
            AipLogger.log(
                __('Place order purchase data'),
                this.getProductForm().serializeArray()
            );

            // Get the cart form data if list view
            if (AipView.isListView()) {
                var cartFormData = $(this.o.getButtonId())
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
        initOptionsEvents: function() {
            if (this.hasOptions()) {
                // Prepare the variables
                var options = this.o.jsConfig.product.options;

                // Set the options events and default values
                for (var i = 0; i < options.length; i++) {
                    // Prepare the fields
                    var option = options[i];
                    var sourceField = this.getOptionField(option);

                    // Set the value change events
                    $(sourceField).on('change', function() {
                        var sourceId = '#' + $(this).attr('id');
                        var targetId = 'input[name="super_attribute[' + $(this).data('attribute-id') + ']"]';
                        $(targetId).val($(sourceId).val());
                    });
                }
            }
        },

        /**
         * Product options validation.
         */
        validateOptions: function() {
            if (this.hasOptions()) {
                return this.getOptionsErrors().length == 0;
            }

            return true;
        },

        /**
         * Check if a product has options.
         */
        hasOptions: function() {
            return this.o.jsConfig.product.options.length
            && this.o.jsConfig.product.options.length > 0;
        },

        /**
         * Check if a product options are valid.
         */
        getOptionsErrors: function() {
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
        isOptionInvalid: function(option) {
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
        getOptionField: function(option) {            
            return this.optionSelectorPrefix
            + this.o.jsConfig.product.id
            + '-' + option['attribute_id'];
        },

        /**
         * Check if a product box should be rendered.
         */
        needsProductBox: function() {    
            return this.o.jsConfig.popups.show_product
            && this.hasOptions() 
            && !this.validateOptions();
        },

        /**
         * Render a product box.
         */
        renderBox: function() {
            // Prepare the parameters
            var self = this;
            var params = {
                product_id: this.o.jsConfig.product.id,
                form_key: this.o.jsConfig.product.form_key
            };

            // Send the AJAX request
            $.ajax({
                type: 'POST',
                url: UrlBuilder.build(self.productDataUrl),
                data: params,
                success: function(data) {
                    // Get the HTML content
                    $(self.productBoxContainerSelector).html(data.html);

                    // Update the selected product options values
                    self.updateSelectedOptionsValues();
                },
                error: function(request, status, error) {
                    AipLogger.log(
                        __('Error retrieving the confimation window product box'),
                        error
                    );
                }
            });
        },

        /**
         * Update the selected product options values.
         */
        updateSelectedOptionsValues: function() {
            if (this.hasOptions()) {
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
        displayErrors: function() {
            // Prepare variables
            var self = this;
            var button = $(this.o.getButtonId());

            // Clear previous errors
            self.clearErrors();

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
        clearErrors: function() {
            $(this.o.getButtonId()).removeClass(this.buttonErrorClass);
            $(this.popoverSelector).remove();
        }
    };
});
