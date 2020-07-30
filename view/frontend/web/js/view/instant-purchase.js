/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'ko',
    'jquery',
    'underscore',
    'uiComponent',
    'aiiCore',
    'Magento_Ui/js/modal/confirm',
    'Magento_Customer/js/customer-data',
    'Naxero_AdvancedInstantPurchase/js/model/authentication-popup',
    'mage/url',
    'mage/template',
    'mage/translate',
    'text!Naxero_AdvancedInstantPurchase/template/confirmation.phtml',
    'mage/validation'
], function (ko, $, _, Component, AiiCore, ConfirmModal, CustomerData, AuthPopup, UrlBuilder, MageTemplate, $t, ConfirmationTemplate) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_InstantPurchase/instant-purchase',
            buttonText: $t('Instant Purchase'),
            purchaseUrl: UrlBuilder.build('instantpurchase/button/placeOrder'),
            showButton: false,
            paymentToken: null,
            shippingAddress: null,
            billingAddress: null,
            shippingMethod: null,
            productFormSelector: '#product_addtocart_form',
            confirmationTitle: $t('Instant Purchase Confirmation'),
            confirmationData: {
                message: $t('Are you sure you want to place order and pay?'),
                shippingAddressTitle: $t('Shipping Address'),
                billingAddressTitle: $t('Billing Address'),
                paymentMethodTitle: $t('Payment Method'),
                shippingMethodTitle: $t('Shipping Method')
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
            this.showButton(data.available);
            this.paymentToken(data.paymentToken);
            this.shippingAddress(data.shippingAddress);
            this.billingAddress(data.billingAddress);
            this.shippingMethod(data.shippingMethod);
        },

        /**
         * Bypass the logged in requirement.
         */
        bypassLogin: function() {
            // Get the cart local storage
            var cartData = CustomerData.get('cart')();

            // Check bypass login
            if (cartData && cartData.hasOwnProperty('advanced-instant-purchase')) {
                var aii = cartData['advanced-instant-purchase'];
                return aii.general.enabled && aii.guest.show_guest_button;
            }

            return false;
        },

        /**
         * Check if customer is logged in.
         */
        isLoggedIn: function() {
            var customer = CustomerData.get('customer')();
            return customer.fullname && customer.firstname;
        },

        /**
         * Handle the button click event.
         */
        handleButtonClick: function() {
            // Get the cart local storage
            var cartData = CustomerData.get('cart')();

            // Check button click event
            if (cartData && cartData.hasOwnProperty('advanced-instant-purchase')) {
                var aii = cartData['advanced-instant-purchase'];

                // Handle the button click logic
                if (this.isLoggedIn()) {
                    this.purchasePopup();
                } else {
                    switch(aii.guest.click_event) {
                        case 'popup':
                            this.loginPopup();
                        break;

                        case 'redirect':
                            this.loginRedirect();
                        break;
                    }
                }
            }
        },

        /**
         * Create a login popup.
         */
        loginPopup: function() {
            AuthPopup.createPopUp('.block-authentication');
            AuthPopup.showModal();
        },

        /**
         * Create a login redirection.
         */
        loginRedirect: function() {
            var loginUrl = UrlBuilder.build('customer/account/login');
            window.location.href = loginUrl;
        },

        /**
         * Get the button state.
         */
        shouldDisableButton: function() {
            // Get the cart local storage
            var cartData = CustomerData.get('cart')();
            $('.aii-button').prop('disabled', true);

            // Check the button state configs
            if (cartData && cartData.hasOwnProperty('advanced-instant-purchase')) {
                var aii = cartData['advanced-instant-purchase'];
                if (aii.guest.click_event !== 'disabled') {
                    $('.aii-button').prop('disabled', false);
                }
            }
        },
        
        /**
         * Purchase popup.
         */
        purchasePopup: function() {
            var form = $(this.productFormSelector),
                confirmTemplate = MageTemplate(ConfirmationTemplate),
                confirmData = _.extend({}, this.confirmationData, {
                    paymentToken: this.paymentToken().summary,
                    shippingAddress: this.shippingAddress().summary,
                    billingAddress: this.billingAddress().summary,
                    shippingMethod: this.shippingMethod().summary,
                    lists: {
                        //tokens: this.getPaymentTokenList(),
                        addresses: {
                            //billing: CustomerData.getBillingAddressList(),
                            //shipping: CustomerData.getShippingAddressList()
                        }
                        //shippingMethods: this.getShippingMethodList(),
                        //paymentMethods: this.getPaymentMethodList(),
                    }
                });

                
            // Todo - Check the validation rules
            /*if (!(form.validation() && form.validation('isValid'))) {
                return;
            }*/

            ConfirmModal({
                title: this.confirmationTitle,
                content: confirmTemplate({
                    data: confirmData
                }),
                actions: {
                    confirm: function() {
                        $.ajax({
                            url: this.purchaseUrl,
                            data: form.serialize(),
                            type: 'post',
                            dataType: 'json',
                            beforeSend: function() {
                                $('body').trigger('processStart');
                            }
                        }).always(function () {
                            $('body').trigger('processStop');
                        });
                    }.bind(this)
                }
            });
        }
    });
});
