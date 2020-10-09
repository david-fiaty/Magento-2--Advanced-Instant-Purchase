/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate',
    'uiComponent',
    'mage/url',
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
], function ($, __, Component, UrlBuilder, AipValidation, AipButton, AipModal, AipUtil, AipLogin, AipSelect, AipSlider, AipAgreement) {
    'use strict';
    
    return Component.extend({
        defaults: {
            aipConfig: window.advancedInstantPurchase,
            user: {},
            uuid: null,
            confirmUrl: 'naxero-aip/ajax/confirmation',
            showButton: false,
            paymentToken: null,
            shippingAddress: null,
            billingAddress: null,
            shippingMethod: null,
            popupContentSelector: '#aip-confirmation-content',
            buttonSelector: '.aip-button',
            isSubView: false,
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
            var self = this;
            $(this.buttonSelector).prop('disabled', false);
            $(this.buttonSelector).on('click', function(e) {
                self.handleButtonClick(e);
            }); 
        },

        /**
         * Log data to the browser console.
         *
         * @param {Object} data
         */
        log: function(data) {
            if (this.aipConfig.general.debug_enabled && this.aipConfig.general.console_logging_enabled) {
                console.log(data);
            }
        },

        /**
         * Check if customer is logged in.
         */
        isLoggedIn: function() {
            return this.aipConfig.user.connected;
        },

        /**
         * Handle the button click event.
         */
        handleButtonClick: function(e) {
            if (this.isLoggedIn()) {
                this.purchasePopup(e);
            } else {
                var val = this.aipConfig.guest.click_event;
                var fn = 'login' + val.charAt(0).toUpperCase() + val.slice(1);
                AipLogin[fn]();
            }
        },

        /**
         * Check the current product view.
         */
        isListView: function() {
            return this.aipConfig.isListView;
        },

        /**
         * Get the confirmation page content.
         */
        getConfirmContent: function(e) {
            // Get the product id
            var pid = $(e.currentTarget)
            .closest('.aip-button-container')
            .attr('id').split('-')[1];

            // Prepare the parameters
            var self = this;
            var params = {
                action: 'Confirmation',
                pid: pid
            };                       

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
                    AipButton.setValidationEvents();
                },
                error: function (request, status, error) {
                    self.log(error);
                }
            });
        },

        /**
         * Purchase popup.
         */
        purchasePopup: function(e) {
            // Prepare variables
            var errors = [];
            var form = AipUtil.getCurrentForm(this.isSubView);

            // Validate the product options
            if (this.isListView() && this.aipConfig.display.product_list) {
                errors = AipValidation.checkOptions(this, e);
            }
            
            // Check the validation rules
            var condition1 = form.validation() && form.validation('isValid');
            var condition2 = errors.length == 0;
            if (!condition1 || !condition2) {
                return;
            }

            // Open the modal
            AipModal.build(this);

            // Get the AJAX content
            this.getConfirmContent(e);
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
                    self.log(error);
                }
            });
        }
    });
});
