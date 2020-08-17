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
    'aiiCore',
    'select2',
    'Magento_Ui/js/modal/confirm',
    'Magento_Customer/js/customer-data',
    'Naxero_AdvancedInstantPurchase/js/model/authentication-popup',
    'mage/url',
    'mage/template',
    'text!Naxero_AdvancedInstantPurchase/template/confirmation.phtml',
    'mage/validation',
    'mage/cookies'
], function (ko, $, _, __, Component, AiiCore, select2, ConfirmModal, CustomerData, AuthPopup, UrlBuilder, MageTemplate, ConfirmationTemplate) {
    'use strict';

    const COOKIE_NAME = 'aaiReopenPurchasePopup';
    const CONFIRMATION_URL = 'aii/ajax/confirmation';
    const LOGIN_URL = 'customer/account/login';
    const AII_SECTION_NAME = 'advancedInstantPurchase';

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
            paymentMethodListClass: 'aii-payment-method-select',
            cardIconClass: 'aii-card-icon',
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
            var aiiConfig = CustomerData.get(AII_SECTION_NAME);

            this._super();
            this.setPurchaseData(instantPurchase());
            this.setConfigData(aiiConfig());

            instantPurchase.subscribe(this.setPurchaseData, this);
            aiiConfig.subscribe(this.setConfigData, this);
            this.aaiConfig = AiiCore.aiiConfig;
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
            if (this.aiiConfig.general.debug_enabled && this.aiiConfig.general.console_logging_enabled) {
                console.log(data);
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
         * Set the config data.
         *
         * @param {Object} data
         */
        setConfigData: function (data) {
            this.aiiConfig = data;
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
            var customer = CustomerData.get('customer')();
            return customer.fullname && customer.firstname;
        },

        /**
         * Handle the button click event.
         */
        handleButtonClick: function() {
            // Handle the button click logic
            if (this.isLoggedIn()) {
                $.cookie(COOKIE_NAME, 'false');
                this.purchasePopup();
            } else {
                switch(this.aiiConfig.guest.click_event) {
                    case 'popup':
                        this.loginPopup();
                    break;

                    case 'redirect':
                        this.loginRedirect();
                    break;
                }
            }
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
            // Disable the button by default
            $(this.buttonSelector).prop('disabled', true);

            // Check the button state configs
            if (this.aiiConfig.guest.click_event !== 'disabled') {
                $(this.buttonSelector).prop('disabled', false);
            }
        },

        /**
         * Format a card icon.
         */
        formatIcon: function(state) {
            if (!state.id || !state.element.parentElement.className.includes(this.paymentMethodListClass)) {
                return state.text;
            }
            var iconUrl = state.element.value.split('*~*')[1];
            var iconHtml = $(
                '<span class="' + this.cardIconClass + '">'
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
                    AiiCore.log.log(error);
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
