/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate',
    'uiComponent',
    'mage/url',
    'Naxero_AdvancedInstantPurchase/js/view/core',
    'mage/validation',
    'mage/cookies',
    'domReady!'
], function($, __, Component, UrlBuilder, Core) {
    'use strict';
    
    return Component.extend({
        defaults: {
            jsConfig: {},
            uuid: null,
            confirmUrl: 'naxero-aip/ajax/confirmation',
            showButton: false,
            buttonContainerSelector: '.aip-button-container',
            popupContentSelector: '#aip-confirmation-content',
            isSubView: false,
            showSubmitButton: true,
            loader: '',
            confirmationData: {
                message: __('Are you sure you want to place order and pay?'),
                shippingAddressTitle: __('Shipping Address'),
                billingAddressTitle: __('Billing Address'),
                paymentMethodTitle: __('Payment Method'),
                shippingMethodTitle: __('Shipping Method')
            }
        },

        /** @inheritdoc */
        initialize: function() {
            this._super();
            this.o = Core.init(this);
            this.build();
        },

        /**
         * Prepare the purchase data.
         *
         * @param {Object} data
         */
        build: function() {
            // Load CSS
            this.o.header.setHeader();

            // Spinner icon
            this.o.spinner.loadIcon();

            // Options validation
            this.o.product.initOptionsEvents();

            // Initialise the UI Logger tree if needed
            this.buildDataTree();

            // Button click event
            var self = this;
            $(this.getButtonId()).on('click touch', function(e) {
                self.handleButtonClick(e);
            });
            
            // Log the step
            this.o.logger.log(
                __('Configuration loaded for product id %1').replace(
                    '%1',
                    this.jsConfig.product.id
                )
            );
        },

        /**
         * Build a browsable tree with log data.
         */
        buildDataTree: function() {
            // Prepare variables
            var self = this;
            var params = {
                product_id: this.jsConfig.product.id,
                form_key: this.jsConfig.product.form_key
            };

            // Set the data viewer button event
            $(this.o.logger.getButtonSelector()).on('click touch', function(e) {
                // Prevent propagation
                e.stopPropagation();

                // Slider view
                self.o.slider.toggleView(e);
                
                // Modal window
                self.showSubmitButton = false;
                self.o.modal.build();
                
                // Send the request
                self.o.slider.showLoader();
                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: UrlBuilder.build(self.o.logger.logsUrl),
                    data: params,
                    success: function(data) {
                        // Get the HTML content
                        self.o.modal.addHtml(
                            self.o.slider.nextSlideSelector,
                            data.html
                        );

                        // Build the data tree
                        self.o.tree.build();
                    },
                    error: function(request, status, error) {
                        self.o.logger.log(
                            __('Error retrieving the UI logging data'),
                            error
                        );
                    }
                });
            });
        },

        /**
         * Handle the button click event.
         */
        handleButtonClick: function(e) {
            // Force Login 
            if (!this.o.login.isLoggedIn()) {
                this.o.login.loginPopup(); 
                return;              
            }

            // Block and list views
            if (this.o.view.isBlockView() || this.o.view.isListView()) {
                // Validate the product options if needed
                var optionsValid = this.o.product.validateOptions();
                if (!optionsValid) {
                    // Display the errors
                    this.o.product.clearErrors();
                    this.o.product.displayErrors(); 
                    return;
                }
            }        
            
            // Page view and/or all conditions valid
            this.purchasePopup(e);
        },

        /**
         * Get the current purchase button id.
         */
        getButtonId: function() {
            return this.jsConfig.product.button_selector;
        },

        /**
         * Get the confirmation page content.
         */
        getConfirmContent: function() {
            // Prepare the parameters
            var self = this;
            var params = {
                action: 'Confirmation',
                product_id: this.jsConfig.product.id,
                form_key: this.jsConfig.product.form_key
            };

            // Log the parameters
            this.o.logger.log(
                __('Confirmation window request parameters'),
                params
            );

            // Send the request
            this.o.slider.showLoader();
            $.ajax({
                type: 'POST',
                cache: false,
                url: UrlBuilder.build(self.confirmUrl),
                data: params,
                success: function(data) {
                    // Get the HTML content
                    self.o.modal.addHtml(self.popupContentSelector, data.html);

                    // Render the product box
                    self.o.product.renderBox();

                    // Initialise the select lists
                    self.o.select.build();

                    // Agreements events
                    self.o.agreement.build();
                    
                    // Set the slider events
                    self.o.slider.build();

                    // Set the additional validation events
                    self.o.button.setValidationEvents();

                    // Log the purchase data
                    self.o.logger.log(
                        __('Purchase data on page load'),
                        self.o.product.getProductForm().serializeArray()
                    );
                },
                error: function(request, status, error) {
                    self.o.logger.log(
                        __('Error retrieving the confimation window data'),
                        error
                    );
                }
            });
        },

        /**
         * Purchase popup.
         */
        purchasePopup: function(e) {
            // Get the current form
            var form = this.o.product.getProductForm();

            // Validate the product options
            // Todo - fix this
            var errors = [];
            //var errors = AipValidation.validateOptions(this);

            // Check the validation rules
            var condition1 = form.validation() && form.validation('isValid');
            var condition2 = errors.length == 0;
            if (!condition1 || !condition2) {
                return;
            }

            // Open the modal
            this.o.modal.build();

            // Get the AJAX content
            this.getConfirmContent();
        },

        /**
         * Get instant purchase data.
         */
        getData: function(fn) {
            var data = this[fn]();
            var ok = data
            && data.hasOwnProperty('summary')
            && typeof data.summary !== 'undefined'
            && data.summary.length > 0;

            return ok ? data.summary : ' ';
        },

        /**
         * Get a form.
         */
        getForm: function(e) {
            var self = this;
            var params = {
                action: $(e.currentTarget).data('form')
            };
            $.ajax({
                type: 'POST',
                cache: false,
                url: UrlBuilder.build(self.confirmUrl),
                data: params,
                success: function(data) {
                    if (params.action == 'Card') {
                        /*
                        window.aipData = {
                            currency: ,
                            amount: ,
                            productId: ,
                            customerId:
                            customerEmail: ,
                        }
                        */
                    }

                    self.o.modal.addHtml(self.o.slider.nextSlideSelector, data.html);
                    $(self.o.button.submitButtonSelector).prop(
                        'disabled',
                        false
                    );
                },
                error: function(request, status, error) {
                    self.o.logger.log(
                        __('Error retrieving the form data'),
                        error
                    );
                }
            });
        }
    });
});
