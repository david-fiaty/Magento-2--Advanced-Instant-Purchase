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
    'Naxero_AdvancedInstantPurchase/js/view/helpers/modal',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/util',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/login',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/select',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/slider',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/product',
    'mage/validation',
    'mage/cookies',
    'domReady!'
], function (ko, $, _, __, Component, UrlBuilder, CustomerData, AiiModal, AiiUtil, AiiLogin, AiiSelect, AiiSlider, AiiProduct) {
    'use strict';

    return Component.extend({
        defaults: {
            aiiConfig: window.advancedInstantPurchase,
            template: 'Magento_InstantPurchase/instant-purchase',
            buttonText: '',
            confirmUrl: 'aii/ajax/confirmation',
            showButton: false,
            paymentToken: null,
            shippingAddress: null,
            billingAddress: null,
            shippingMethod: null,
            popupContentSelector: '#aii-confirmation-content',
            buttonSelector: '.aii-button',
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

            console.log(data);

            // Prepare the data
            this.showButton(data.available);
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
            if (this.aiiConfig.general.debug_enabled && this.aiiConfig.general.console_logging_enabled) {
                console.log(data);
            }
        },

        /**
         * Bypass the logged in requirement.
         */
        bypassLogin: function() {
            return this.aiiConfig.general.enabled
            && this.aiiConfig.guest.show_guest_button;
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
        handleButtonClick: function() {
            var val = this.aiiConfig.guest.click_event;
            if (this.isLoggedIn()) {
                this.purchasePopup();
            } else {
                var fn = 'login' + val.charAt(0).toUpperCase() + val.slice(1);
                AiiLogin[fn]();
            }
        },

        /**
         * Get the button state.
         */
        shouldDisableButton: function() {
            // Get the cart local storage
            $(this.buttonSelector).prop('disabled', true);

            // Check the button state configs
            if (this.aiiConfig.guest.click_event !== 'disabled') {
                $(this.buttonSelector).prop('disabled', false);
            }
        },

        /**
         * Get the confirmation page content.
         */
        getConfirmContent: function() {
            var self = this;
            AiiSlider.showLoader(self);
            var params = {
                action: 'Confirmation'
            };
            $.ajax({
                type: 'POST',
                url: UrlBuilder.build(self.confirmUrl),
                data: params,
                success: function (data) {
                    // Get the HTML content
                    $(self.popupContentSelector).html(data.html);

                    // Load the product view
                    AiiProduct.loadBoxView(self.popupContentSelector);

                    // Initialise the select lists
                    AiiSelect.build(self);

                    // Set the slider events
                    AiiSlider.build();
                },
                error: function (request, status, error) {
                    self.log(error);
                }
            });
        },

        /**
         * Purchase popup.
         */
        purchasePopup: function() {
            var form = AiiUtil.getCurrentForm(self.isSubView),
            confirmData = _.extend({}, this.confirmationData, {
                paymentToken: this.getData('paymentToken'),
                shippingAddress: this.getData('shippingAddress'),
                billingAddress: this.getData('billingAddress'),
                shippingMethod: this.getData('shippingMethod')
            });

            // Check the validation rules
            if (!(form.validation() && form.validation('isValid'))) {
                return;
            }

            // Open the modal
            AiiModal.getConfirmModal(confirmData, this);

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
                url: UrlBuilder.build(self.confirmUrl),
                data: params,
                success: function (data) {
                    if (params.action == 'Card') {
                        /*
                        window.aiiData = {
                            currency: ,
                            amount: ,
                            productId: ,
                            customerId: 
                            customerEmail: ,
                        }
                        */         
                    }

                    $(AiiSlider.nextSlideSelector).html(data.html);
                },
                error: function (request, status, error) {
                    self.log(error);
                }
            });
        }
    });
});
