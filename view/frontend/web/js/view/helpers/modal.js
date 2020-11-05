define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/modal',
    'Naxero_BuyNow/js/view/helpers/template',
    'Naxero_BuyNow/js/view/helpers/slider',
    'Naxero_BuyNow/js/view/helpers/product',
    'Naxero_BuyNow/js/view/helpers/logger',
    'Naxero_BuyNow/js/view/helpers/message',
    'Naxero_BuyNow/js/view/helpers/paths'
], function ($, __, modal, AipTemplate, AipSlider, AipProduct, AipLogger, AipMessage, AipPaths) {
    'use strict';

    return {
        modalWrapperSelector: '.modal-inner-wrap',
        submitButtonSelector: '.aip-submit',
        submitButtonClasses: 'action-primary action-accept aip-submit',
        cancelButtonSelector: '.action-close',
        loginBlockSelector: '.block-authentication',
        cancelButtonClasses: 'action-secondary action-dismiss',
        orderUrl: 'order/request',

        /**
         * Initialise the object.
         */
        init: function (obj) {
            this.o = obj;
            return this;
        },

        /**
         * Add HTML to a container.
         */
        addHtml: function (target, html) {
            $(target).html(html);
            $(this.modalWrapperSelector).animate(
                {minHeight: $(target).height()  + 'px'}
                ,
                300
            );
        },

        /**
         * Get the confirmation page modal popup.
         */
        getOrderModal: function () {
            var self = this;

            modal({
                title: this.o.jsConfig.popups.popup_title,
                innerScroll: true,
                modalClass: 'aip-modal',
                modalContent: AipTemplate.getConfirmation({}),
                buttons: [{
                    text: __('Cancel'),
                    class: self.cancelButtonClasses,
                    click: function (e) {
                        $(self.cancelButtonSelector).trigger('click');
                    }
                },
                {
                    text: self.o.jsConfig.popups.popup_confirm_button_text,
                    class: self.submitButtonClasses,
                    click: function (e) {
                        AipSlider.showLoader();
                        $.ajax({
                            cache: false,
                            url: AipPaths.get(self.orderUrl),
                            data: AipProduct.getProductFormData(),
                            type: 'post',
                            dataType: 'json',
                            success: function (data) {
                                AipMessage.checkResponse(data, e);
                            },
                            error: function (request, status, error) {
                                AipLogger.log(
                                    __('Error submitting the form data'),
                                    JSON.stringify(error)
                                );
                            }
                        });
                    }
                }]
            });
        },

        /**
         * Get the login modal popup.
         */
        getLoginModal: function () {
            var options = {
                'type': 'popup',
                'modalClass': 'popup-authentication',
                'focus': '[name=username]',
                'responsive': true,
                'innerScroll': true,
                'trigger': '.proceed-to-checkout, .aip-login-popup',
                'buttons': []
            };

            modal(options, $(this.loginBlockSelector));
            $(this.modalWindow).Modal('openModal').trigger('contentUpdated');
        },

        /**
         * Get the logger modal popup.
         */
        getLoggerModal: function (e) {
            var self = this;
            var title = this.o.jsConfig.module.title + ' ' + __('Logger');

            $(e.currentTarget).modal({
                title: title,
                innerScroll: true,
                modalClass: 'aip-modal',
                modalContent: AipTemplate.getLogger({}),
                buttons: [{
                    text: __('Close'),
                    class: self.cancelButtonClasses,
                    click: function (e) {
                        $(self.cancelButtonSelector).trigger('click');
                    }
                }]
            });
        }
    };
});
