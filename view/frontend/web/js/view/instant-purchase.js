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
    'Magento_Ui/js/modal/confirm',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/view/shipping',
    'Naxero_AdvancedInstantPurchase/js/model/authentication-popup',
    'mage/url',
    'mage/template',
    'text!Naxero_AdvancedInstantPurchase/template/confirmation',
    'select2',
    'mage/validation',
    'mage/cookies',
    'domReady!'
], function (ko, $, _, __, Component, ConfirmModal, CustomerData, ShippingView, AuthPopup, UrlBuilder, MageTemplate, ConfirmationTemplate, select2) {
    'use strict';

    const COOKIE_NAME = 'aaiReopenPurchasePopup';
    const CONFIRMATION_URL = 'aii/ajax/confirmation';
    const LOGIN_URL = 'customer/account/login';
    const AII_SECTION_NAME = 'advancedInstantPurchase';
    const LOADER_ICON = 'Naxero_AdvancedInstantPurchase/images/ajax-loader.gif';

    return Component.extend({
        defaults: {
            aiiConfig: {},
            template: 'Magento_InstantPurchase/instant-purchase',
            buttonText: '',
            purchaseUrl: UrlBuilder.build('instantpurchase/button/placeOrder'),
            showButton: false,
            paymentToken: null,
            shippingAddress: null,
            billingAddress: null,
            shippingMethod: null,
            productFormSelector: '#product_addtocart_form',
            popupContentSelector: '#aii-confirmation-content',
            buttonSelector: '.aii-button',
            listSelector: '.aii-select',
            loginBlockSelector: '.block-authentication',
            confirmationTitle: __('Instant Purchase Confirmation'),
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
         * Log data to the browser console.
         *
         * @param {Object} data
         */
        log: function(data) {
            // Get the cart local storage
            var cartData = CustomerData.get('cart')();

            // Check console logging enabled
            if (cartData && cartData.hasOwnProperty(AII_SECTION_NAME)) {
                var aii = cartData[AII_SECTION_NAME];
                if (aii.general.debug_enabled && aii.general.console_logging_enabled) {
                    console.log(data);
                }
            }
        },

        /**
         * Set data from CustomerData.
         *
         * @param {Object} data
         */
        setPurchaseData: function(data) {
            // Prepare the data
            this.showButton(data.available);
            this.paymentToken(data.paymentToken);
            this.shippingAddress(data.shippingAddress);
            this.billingAddress(data.billingAddress);
            this.shippingMethod(data.shippingMethod);

            // Cookie for after login process
            if ($.cookie(COOKIE_NAME) === 'true') {
                $(this.buttonSelector).trigger('click');
            }
        },

        /**
         * Bypass the logged in requirement.
         */
        bypassLogin: function() {
            // Get the cart local storage
            var cartData = CustomerData.get('cart')();

            // Check bypass login
            if (cartData && cartData.hasOwnProperty(AII_SECTION_NAME)) {
                var aii = cartData[AII_SECTION_NAME];
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
         * Get the loader icon.
         */
        getLoaderIconPath: function() {
            console.log(require.toUrl(LOADER_ICON));
            return require.toUrl(LOADER_ICON);
        },

        /**
         * Handle the button click event.
         */
        handleButtonClick: function() {
            //ShippingView.getPopUp();

            /*
            // Get the cart local storage
            var cartData = CustomerData.get('cart')();

            // Handle button click
            if (cartData && cartData.hasOwnProperty(AII_SECTION_NAME)) {
                var aii = cartData[AII_SECTION_NAME];

                // Handle the button click logic
                if (this.isLoggedIn()) {
                    $.cookie(COOKIE_NAME, 'false');
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

        */
        },

        /**
         * Create a login popup.
         */
        loginPopup: function() {
            $.cookie(COOKIE_NAME, 'true');
            AuthPopup.createPopUp(this.loginBlockSelector);
            AuthPopup.showModal();
        },

        /**
         * Create a login redirection.
         */
        loginRedirect: function() {
            var loginUrl = UrlBuilder.build(LOGIN_URL);
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
            if (cartData && cartData.hasOwnProperty(AII_SECTION_NAME)) {
                var aii = cartData[AII_SECTION_NAME];
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
            var iconUrl = state.element.value.split('*~*')[1];
            var iconHtml = $(
                '<span class="aii-card-icon">'
                + '<img src="' + iconUrl + '">'
                + state.text + '</span>'
            );

            return iconHtml;
        },

        /**
         * Get a card option public hash.
         */
        getOptionPublicHash: function(val) {
            return val.split('*~*')[0];
        },

        /**
         * Get the confirmation page content.
         */
        getConfirmContent: function() {
            var self = this;
            $.ajax({
                type: 'POST',
                url: UrlBuilder.build(CONFIRMATION_URL),
                success: function (data) {
                    // Get the HTML content
                    $(self.popupContentSelector).append(data.html);

                    // Initialise the select lists
                    $(self.listSelector).select2({
                        language: 'en',
                        theme: 'classic',
                        templateResult: self.formatIcon,
                        templateSelection: self.formatIcon
                    });

                    // Set the lists events
                    $(self.listSelector).on('change', function () {
                        var targetField = $(this).attr('data-field');
                        var fieldValue = $(this).data('field') == 'instant_purchase_payment_token'
                        ? self.getOptionPublicHash(fieldValue)
                        : fieldValue;
                        $('input[name="' + targetField + '"]').val(fieldValue);
                    });
                },
                error: function (request, status, error) {
                    self.log(error);
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

            // Check the validation rules
            if (!(form.validation() && form.validation('isValid'))) {
                return;
            }

            // Open the modal
            this.getConfirmModal(confirmData, form);

            // Get the AJAX content
            this.getConfirmContent();
        }
    });
});
