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
    'select2',
    'Magento_Ui/js/modal/confirm',
    'Magento_Customer/js/customer-data',
    'Naxero_AdvancedInstantPurchase/js/model/authentication-popup',
    'mage/url',
    'mage/template',
    'mage/translate',
    'text!Naxero_AdvancedInstantPurchase/template/confirmation.phtml',
    'mage/validation'
], function (ko, $, _, Component, AiiCore, select2, ConfirmModal, CustomerData, AuthPopup, UrlBuilder, MageTemplate, $t, ConfirmationTemplate) {
    'use strict';

    const SECTION_NAME = 'advancedInstantPurchase';

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
            buttonSelector: '.aii-button',
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
            if (cartData && cartData.hasOwnProperty(SECTION_NAME)) {
                var aii = cartData[SECTION_NAME];
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
            if (cartData && cartData.hasOwnProperty(SECTION_NAME)) {
                var aii = cartData[SECTION_NAME];

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
            $(this.buttonSelector).prop('disabled', true);

            // Check the button state configs
            if (cartData && cartData.hasOwnProperty(SECTION_NAME)) {
                var aii = cartData[SECTION_NAME];
                if (aii.guest.click_event !== 'disabled') {
                    $(this.buttonSelector).prop('disabled', false);
                }
            }
        },

        /**
         * Format a card icon.
         */
        formatIcon: function(state) {
            if (!state.id || !state.element.parentElement.className.includes('aii-payment-method-select')) {
                return state.text;
            }

            var imageHtml = $(
                '<span><img src="'
                + this.getOptionIconUrl(state.element.value)
                + '" class="img-flag" /> ' + state.text + '</span>'
            );

            return imageHtml;
        },

        /**
         * Get a card option public hash.
         */
        getOptionPublicHash: function(val) {
            return val.split('*~*')[0];
        },

        /**
         * Get a card option icon URL.
         */
        getOptionIconUrl: function(val) {
            return val.split('*~*')[1];
        },

        /**
         * Get the confirmation page content.
         */
        getConfirmContent: function() {
            var self = this;
            $.ajax({
                type: 'POST',
                url: UrlBuilder.build('aii/ajax/confirmation'),
                success: function (data) {
                    // Get the HTML content
                    $('#aii-confirmation-content').append(data.html);

                    // Initialise the select lists
                    $('.aii-select').select2({
                        language: 'en',
                        theme: 'classic',
                        placeholder: $t('Select an option'),
                        templateResult: self.formatIcon,
                        templateSelection: self.formatIcon
                    });

                    // Set the lists events
                    $('.aii-select').on('change', function () {
                        var targetField = $(this).attr('data-field');
                        var fieldValue = $(this).data('field') == 'instant_purchase_payment_token'
                        ? self.getOptionPublicHash(fieldValue)
                        : fieldValue;
                        $('input[name="' + targetField + '"]').val(fieldValue);
                    });
                },
                error: function (request, status, error) {
                    console.log(error);
                }
            });
        },

        /**
         * Get the confirmation page modal popup.
         */
        getConfirmModal: function(confirmData, form) {
            var confirmTemplate = MageTemplate(ConfirmationTemplate);
            ConfirmModal({
                title: this.confirmationTitle,
                clickableOverlay: true,
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
        },

        /**
         * Purchase popup.
         */
        purchasePopup: function() {
            var form = $(this.productFormSelector),
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

            // Open the modal
            this.getConfirmModal(confirmData, form);

            // Get the AJAX content
            this.getConfirmContent();
        }
    });
});
