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
    'text!Magento_InstantPurchase/template/confirmation.html',
    'mage/validation'
], function (ko, $, _, Component, aiiCore, confirm, customerData, authPopup, urlBuilder, mageTemplate, $t, confirmationTemplate) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_InstantPurchase/instant-purchase',
            buttonText: $t('Instant Purchase'),
            purchaseUrl: urlBuilder.build('instantpurchase/button/placeOrder'),
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
        initialize: function () {
            var instantPurchase = customerData.get('instant-purchase');
            this._super();
            this.setPurchaseData(instantPurchase());
            instantPurchase.subscribe(this.setPurchaseData, this);
        },

        /** @inheritdoc */
        initObservable: function () {
            this._super()
                .observe('showButton paymentToken shippingAddress billingAddress shippingMethod');

            return this;
        },

        /**
         * Set data from customerData.
         *
         * @param {Object} data
         */
        setPurchaseData: function (data) {
            this.showButton(data.available);
            this.paymentToken(data.paymentToken);
            this.shippingAddress(data.shippingAddress);
            this.billingAddress(data.billingAddress);
            this.shippingMethod(data.shippingMethod);
        },

        /**
         * Bypass the logged in requirement
         */
        bypassLogin: function () {

            var cartData = customerData.get('cart')();

            console.log('cartData');
            console.log(cartData);

            return cartData['advanced-instant-purchase'].general.enabled
            && cartData['advanced-instant-purchase'].guest.show_guest_button;
        },

        /**
         * Check if customer is logged in
         */
        isLoggedIn: function () {
            var customerData = customerData.get('customer')();
            return customerData.fullname && customerData.firstname;
        },

        /**
         * Login popup.
         */
        loginPopup: function () {
            authPopup.createPopUp('.block-authentication');
            authPopup.showModal();
        },
        
        /**
         * Purchase popup
         */
        purchasePopup: function () {
            var form = $(this.productFormSelector),
                confirmTemplate = mageTemplate(confirmationTemplate),
                confirmData = _.extend({}, this.confirmationData, {
                    paymentToken: this.paymentToken().summary,
                    shippingAddress: this.shippingAddress().summary,
                    billingAddress: this.billingAddress().summary,
                    shippingMethod: this.shippingMethod().summary
                });

            // Todo - Check the validation rules
            /*if (!(form.validation() && form.validation('isValid'))) {
                return;
            }*/

            confirm({
                title: this.confirmationTitle,
                content: confirmTemplate({
                    data: confirmData
                }),
                actions: {
                    /** @inheritdoc */
                    confirm: function () {
                        $.ajax({
                            url: this.purchaseUrl,
                            data: form.serialize(),
                            type: 'post',
                            dataType: 'json',

                            /** Show loader before send */
                            beforeSend: function () {
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
