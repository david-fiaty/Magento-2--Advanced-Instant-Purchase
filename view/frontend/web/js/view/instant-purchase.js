/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate',
    'uiComponent',
    'mage/url',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/tree',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/view',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/product',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/spinner',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/logger',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/header',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/button',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/modal',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/login',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/select',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/slider',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/agreement',
    'mage/validation',
    'mage/cookies',
    'domReady!'
], function($, __, Component, UrlBuilder, AipTree, AipView, AipProduct, AipSpinner, AipLogger, AipHeader, AipButton, AipModal, AipLogin, AipSelect, AipSlider, AipAgreement) {
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
            this.build();
        },

        /**
         * Prepare the purchase data.
         *
         * @param {Object} data
         */
        build: function() {
            // Load CSS
            AipHeader.setHeader(this);

            // Purchase button state
            AipButton.setPurchaseButtonState(this);

            // Spinner icon
            AipSpinner.loadIcon(this);

            // Options validation
            AipProduct.initOptionsEvents(this);

            // Initialise the UI Logger tree if needed
            this.buildDataTree();

            // Button click event
            var self = this;
            $(this.getButtonId()).on('click touch', function(e) {
                self.handleButtonClick(e);
            });
            
            // Log the step
            AipLogger.log(
                this,
                __('Configuration loaded for product id %1').replace(
                    '%1',
                    this.jsConfig.product.id
                ),
                this.jsConfig
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
            $(AipLogger.getButtonSelector(this)).on('click touch', function(e) {
                // Prevent propagation
                e.stopPropagation();

                // Slider view
                AipSlider.toggleView(self, e);
                
                // Modal window
                // Todo - fix submit button state
                //obj.showSubmitButton = false;
                AipModal.build(self);
                
                // Send the request
                AipSlider.showLoader(self);
                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: UrlBuilder.build(AipLogger.logsUrl),
                    data: params,
                    success: function(data) {
                        // Get the HTML content
                        AipModal.addHtml(
                            AipSlider.nextSlideSelector,
                            data.html
                        );

                        // Build the data tree
                        AipTree.build(self);
                    },
                    error: function(request, status, error) {
                        AipLogger.log(
                            self,
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
            if (!AipLogin.isLoggedIn(this)) {
                AipLogin.loginPopup(); 
                return;              
            }

            // Block and list views
            if (AipView.isBlockView(this) || AipView.isListView(this)) {
                // Validate the product options if needed
                var optionsValid = AipProduct.validateOptions(this)
                if (!optionsValid) return;
            }        
            
            // Page view and/or all conditions met
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
            AipLogger.log(
                this,
                __('Confirmation window request parameters'),
                params
            );

            // Send the request
            AipSlider.showLoader(self);
            $.ajax({
                type: 'POST',
                cache: false,
                url: UrlBuilder.build(self.confirmUrl),
                data: params,
                success: function(data) {
                    // Get the HTML content
                    AipModal.addHtml(self.popupContentSelector, data.html);

                    // Render the product box
                    AipProduct.renderBox(self);

                    // Initialise the select lists
                    AipSelect.build(self);

                    // Agreements events
                    AipAgreement.build(self);
                    
                    // Set the slider events
                    AipSlider.build();

                    // Set the additional validation event
                    AipButton.setValidationEvents(self);

                    // Log the purchase data
                    AipLogger.log(
                        self,
                        __('Purchase data on page load'),
                        AipProduct.getProductForm(self).serializeArray()
                    );
                },
                error: function(request, status, error) {
                    AipLogger.log(
                        self,
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
            var form = AipProduct.getProductForm(this);

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
            AipModal.build(this);

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

                    AipModal.addHtml(AipSlider.nextSlideSelector, data.html);
                    $(AipButton.submitButtonSelector).prop(
                        'disabled',
                        false
                    );
                },
                error: function(request, status, error) {
                    AipLogger.log(
                        self,
                        __('Error retrieving the form data'),
                        error
                    );
                }
            });
        }
    });
});
