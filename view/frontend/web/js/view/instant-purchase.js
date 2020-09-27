/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'ko',
    'jquery',
    'underscore',
    'mage/translate',
    'uiComponent',
    'mage/url',
    'Magento_Customer/js/customer-data',
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
], function (ko, $, _, __, Component, UrlBuilder, CustomerData, AipValidation, AipButton, AipModal, AipUtil, AipLogin, AipSelect, AipSlider, AipAgreement) {
    'use strict';
    
    return Component.extend({
        defaults: {
            aipConfig: window.advancedInstantPurchase,
            template: 'Magento_InstantPurchase/instant-purchase',
            uuid: null,
            buttonText: '',
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
            var instantPurchase = CustomerData.get('instant-purchase');
            this._super();
            this.setPurchaseData(instantPurchase());
            instantPurchase.subscribe(this.setPurchaseData, this);
        },

        /** @inheritdoc */
        initObservable: function() {
            this._super()
                .observe('showButton paymentToken shippingAddress billingAddress shippingMethod');

            return this;
        },

        /**
         * Set data from CustomerData.
         *
         * @param {Object} data
         */
        setPurchaseData: function(data) {
            // Get the button state
            var showButton = data.available && this.canDisplayButton();

            // Prepare the data
            this.showButton(showButton);
            this.paymentToken(data.paymentToken);
            this.shippingAddress(data.shippingAddress);
            this.billingAddress(data.billingAddress);
            this.shippingMethod(data.shippingMethod);
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
         * Bypass the logged in requirement.
         */
        bypassLogin: function() {
            return this.aipConfig.general.enabled
            && this.aipConfig.guest.show_guest_button;
        },

        /**
         * Check if customer is logged in.
         */
        isLoggedIn: function() {
            var data = CustomerData.get('customer');
            if (data) {
                var customer = data();
                return customer.fullname && customer.firstname;
            }

            return false;
        },

        /**
         * Handle the button click event.
         */
        handleButtonClick: function(obj, e) {
            if (this.isLoggedIn()) {
                this.purchasePopup(obj, e);
            } else {
                var val = this.aipConfig.guest.click_event;
                var fn = 'login' + val.charAt(0).toUpperCase() + val.slice(1);
                AipLogin[fn]();
            }
        },

        /**
         * Get the button state.
         */
        shouldDisableButton: function() {
            // Get the cart local storage
            $(this.buttonSelector).prop('disabled', true);

            // Check the button state configs
            if (this.aipConfig.guest.click_event !== 'disabled') {
                $(this.buttonSelector).prop('disabled', false);
            }
        },

        /**
         * Check the current product view.
         */
        isListView: function() {
            return this.aipConfig.isListView;
        },

        /**
         * Check if the button can be displayed.
         */
        canDisplayButton: function() {
            return (this.aipConfig.display.product_list && this.isListView())
            || (this.aipConfig.display.product_view && !this.isListView());
        },

        /**
         * Get the confirmation page content.
         */
        getConfirmContent: function(obj, e) {
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
        purchasePopup: function(obj, e) {
            // Prepare variables
            var errors = [];
            var form = AipUtil.getCurrentForm(self.isSubView);
            var confirmData = _.extend({}, this.confirmationData, {
                paymentToken: this.getData('paymentToken'),
                shippingAddress: this.getData('shippingAddress'),
                billingAddress: this.getData('billingAddress'),
                shippingMethod: this.getData('shippingMethod')
            });

            // Validate the product options
            if (obj.isListView() && this.aipConfig.display.product_list) {
                errors = AipValidation.checkOptions(obj, e);
            }
            
            // Check the validation rules
            var condition1 = form.validation() && form.validation('isValid');
            var condition2 = errors.length == 0;
            if (!condition1 || !condition2) {
                return;
            }

            // Open the modal
            AipModal.build(confirmData, this);

            // Get the AJAX content
            this.getConfirmContent(obj, e);
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
