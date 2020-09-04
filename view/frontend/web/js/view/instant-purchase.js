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
    'Naxero_AdvancedInstantPurchase/js/model/authentication-popup',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/message',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/util',
    'mage/url',
    'mage/template',
    'text!Naxero_AdvancedInstantPurchase/template/confirmation.html',
    'select2',
    'slick',
    'mage/validation',
    'mage/cookies',
    'domReady!'
], function (ko, $, _, __, Component, ConfirmModal, CustomerData, AuthPopup, AiiMessage, AiiUtil, UrlBuilder, MageTemplate, ConfirmationTemplate, select2, slick) {
    'use strict';

    return Component.extend({
        defaults: {
            aiiConfig: window.advancedInstantPurchase,
            template: 'Magento_InstantPurchase/instant-purchase',
            buttonText: '',
            purchaseUrl: 'instantpurchase/button/placeOrder',
            loginUrl: 'customer/account/logins',
            confirmUrl: 'aii/ajax/confirmation',
            saveAddressUrl: 'customer/address/formPost',
            showButton: false,
            paymentToken: null,
            shippingAddress: null,
            billingAddress: null,
            shippingMethod: null,
            productFormSelector: '#product_addtocart_form',
            popupContentSelector: '#aii-confirmation-content',
            buttonSelector: '.aii-button',
            listSelector: '.aii-select',
            linkSelector: '.aii-new',
            nextSlideSelector: '#aii-next-slide-container',
            loginBlockSelector: '.block-authentication',
            confirmationTitle: __('Instant Purchase Confirmation'),
            confirmationTemplateSelector: '#aii-confirmation-template',
            sliderSelector: '#aii-slider',
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
            // todo - get shipping popup
            // ShippingView.getPopUp();

            // Handle the button click logic
            if (this.isLoggedIn()) {
                this.purchasePopup();
            } else {
                var val = this.aiiConfig.guest.click_event;
                var fn = 'login' + val.charAt(0).toUpperCase() + val.slice(1);
                this[fn]();
            }
        },

        /**
         * Create a login popup.
         */
        loginPopup: function() {
            AuthPopup.createPopUp(this.loginBlockSelector);
            AuthPopup.showModal();
        },

        /**
         * Create a login redirection.
         */
        loginRedirect: function() {
            var loginUrl = UrlBuilder.build(this.loginUrl);
            window.location.href = loginUrl;
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
         * Get the new address form.
         */
        getNewAddressForm: function() {
            var self = this;
            var params = {
                action: 'address'
            };
            $.ajax({
                type: 'POST',
                url: UrlBuilder.build(self.confirmUrl),
                data: params,
                success: function (data) {
                    $(self.nextSlideSelector).html(data.html);
                },
                error: function (request, status, error) {
                    self.log(error);
                }
            });
        },

        /**
         * Get the confirmation page content.
         */
        getConfirmContent: function() {
            var self = this;
            var params = {
                action: 'confirmation'
            };
            AiiUtil.showLoader(self);
            $.ajax({
                type: 'POST',
                url: UrlBuilder.build(self.confirmUrl),
                data: params,
                success: function (data) {
                    // Get the HTML content
                    $(self.popupContentSelector).html(data.html);

                    // Initialise the select lists
                    $(self.listSelector).select2({
                        language: 'en',
                        theme: 'classic',
                        templateResult: self.formatIcon,
                        templateSelection: self.formatIcon
                    });

                    // Set the lists events
                    $(self.listSelector).on('change', function() {
                        var targetField = $(this).attr('data-field');
                        var fieldValue = $(this).data('field') == 'instant_purchase_payment_token'
                        ? self.getOptionPublicHash(fieldValue)
                        : fieldValue;
                        $('input[name="' + targetField + '"]').val(fieldValue);
                    });

                    // Set the slider events
                    $(self.sliderSelector).slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        infinite: false,
                        speed: 300,
                        adaptiveHeight: true,
                        arrows: false
                    });

                    // Set the link events
                    $(self.linkSelector).on('click', function(e) {
                        self.toggleView(e);
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
        getConfirmModal: function(confirmData) {
            var self = this;
            var confirmTemplate = MageTemplate(ConfirmationTemplate);
            ConfirmModal({
                title: this.confirmationTitle,
                innerScroll: true,
                content: confirmTemplate({
                    data: confirmData
                }),
                buttons: [
                {
                    text: __('Cancel'),
                    class: 'action-secondary action-dismiss',
                    click: function(e) {
                        if (self.isSubView) {
                            self.toggleView(e);                        }
                        else {
                            this.closeModal(e);
                        }
                    }
                },
                {
                    text: __('Submit'),
                    class: 'action-primary action-accept',
                    click: function(e) {
                        var btn = this;
                        $.ajax({
                            url: self.getConfirmUrl(),
                            data: self.getCurrentForm().serialize(),
                            type: 'post',
                            dataType: 'json',
                            success: function(data) {
                                console.log(data);
                                AiiMessage.checkResponse(data, self);
                                //btn.closeModal(e);
                            },
                            error: function(request, status, error) {
                                self.log(error);
                            }
                        })
                    }
                }]
            });
        },

        /**
         * Get the modal confirmation URL.
         */
        getConfirmUrl: function() {
            var url = this.isSubView ? this.saveAddressUrl : this.purchaseUrl;
            return UrlBuilder.build(url);
        },

        /**
         * Get the current form.
         */
        getCurrentForm: function() {
            var form = this.isSubView ? '.form-address-edit' : this.productFormSelector;
            return $(form);
        },

        /**
         * Get the current slide.
         */
        getCurrentSlide: function() {
            var slide = (this.isSubView) ? this.nextSlideSelector : this.popupContentSelector;
            return $(slide);
        },

        /**
         * Purchase popup.
         */
        purchasePopup: function() {
            var form = this.getCurrentForm(),
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
            this.getConfirmModal(confirmData);

            // Get the AJAX content
            this.getConfirmContent();
        },

        /**
         * Get the payment token.
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
         * Handles the view switch.
         */
        toggleView: function(e) {
            e.preventDefault();
            AiiUtil.showLoader(this);
            if (this.isSubView) {
                this.getConfirmContent();
                $(this.sliderSelector).slick('slickPrev');
                this.isSubView = false;
                $('.action-dismiss span').text(__('Cancel'));
                $(this.sliderSelector).slick('unslick');
            }
            else {
                $(this.sliderSelector).slick('slickNext');
                $('.action-dismiss span').text(__('Back'));
                $(this.nextSlideSelector).show();
                this.isSubView = true;
                this.getNewAddressForm();
            }
        }
    });
});
