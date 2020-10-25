/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate',
    'uiComponent',
    'mage/url',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/product',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/spinner',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/logger',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/header',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/validation',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/button',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/modal',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/util',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/login',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/select',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/slider',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/agreement',
    'mage/validation',
    'mage/cookies',
    'domReady!'
], function ($, __, Component, UrlBuilder, AipProduct, AipSpinner, AipLogger, AipHeader, AipValidation, AipButton, AipModal, AipUtil, AipLogin, AipSelect, AipSlider, AipAgreement) {
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

            // Loader icon
            AipSpinner.loadIcon(this);

            // Options validation
            // Todo - Fix the options validation logic - Should run only in list mode
            // AipValidation.initOptionsValidation(this);

            // Button click event
            var self = this;
            $(this.getButtonId()).on('click touch', function(e) {
                self.handleButtonClick(e);
            }); 

            // Initialise the UI Logger tree if needed
            AipLogger.buildDataTree(this);

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
         * Handle the button click event.
         */
        handleButtonClick: function(e) {
            // Click event
            if (this.hasOptions() && this.isblockView()) {
                window.location.href = this.jsConfig.product.page_url;
            }
            else if (AipLogin.isLoggedIn(this)) {
                this.purchasePopup(e);
            } else {
                var functionName = 'popup';
                var fn = 'login' + functionName.charAt(0).toUpperCase() + functionName.slice(1);
                AipLogin[fn]();
            }
        },

        /**
         * Check if the current product is in list view.
         */
        isListView: function() {
            return this.jsConfig.product.display == 'list';
        },

        /**
         * Check if the current product is in block view.
         */
        isblockView: function() {
            return this.jsConfig.product.display == 'block'
            || this.jsConfig.product.display == 'widget';
        },

        /**
         * Check if the current product is in list view.
         */
        isListView: function() {
            return this.jsConfig.product.display == 'list';
        },

        /**
         * Check if the current product is in page view.
         */
        isPageView: function() {
            return !this.isblockView() && !this.isListView();
        },

        /**
         * Check if the current product has options.
         */
        hasOptions: function() {
            return this.jsConfig.product.has_options;
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
                success: function (data) {
                    // Get the HTML content
                    AipModal.addHtml(self.popupContentSelector, data.html);

                    // Initialise the select lists
                    AipSelect.build(self);

                    // Agreements events
                    AipAgreement.build(self);
                    
                    // Set the slider events
                    AipSlider.build();

                    // Set the additional validation event
                    // Todo - fix the validation event
                    //AipButton.setValidationEvents(self);

                    // Log the purchase data
                    AipLogger.log(
                        self,
                        __('Purchase data on page load'),
                        AipProduct.getProductForm(self).serializeArray()
                    );
                },
                error: function (request, status, error) {
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
            var form = AipUtil.getCurrentForm(this);

            // Validate the product options
            var errors = AipValidation.validateOptions(this);
            
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
                success: function (data) {
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
                error: function (request, status, error) {
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
