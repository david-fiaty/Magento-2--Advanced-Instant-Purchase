define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/confirm',
    'Naxero_BuyNow/js/view/helpers/template',
    'Naxero_BuyNow/js/view/helpers/slider',
    'Naxero_BuyNow/js/view/helpers/product',
    'Naxero_BuyNow/js/view/helpers/logger',
    'Naxero_BuyNow/js/view/helpers/message',
    'Naxero_BuyNow/js/view/helpers/paths'
], function ($, __, ConfirmModal, AipTemplate, AipSlider, AipProduct, AipLogger, AipMessage, AipPaths) {
    'use strict';

    return {
        modalWrapperSelector: '.modal-inner-wrap',
        submitButtonSelector: '.nbn-submit',
        submitButtonClasses: 'action-primary action-accept nbn-submit',
        cancelButtonSelector: '.action-close',
        cancelButtonSelectorPrefix: '.nbn-button-',
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
        getOrderModal: function (obj) {
            var self = this;
            ConfirmModal({
                title: this.o.jsConfig.popups.popup_title,
                innerScroll: true,
                modalClass: 'nbn-modal',
                content: AipTemplate.getConfirmation({}),
                buttons: [{
                    text: __('Cancel'),
                    class: self.cancelButtonSelectorPrefix + obj.jsConfig.product.id,
                    click: function (e) {
                        $(self.cancelButtonSelector).trigger('click');
                        if (self.o.isSubView) {
                            AipSlider.toggleView(e);
                            var buttonId = '#' + $(e.currentTarget).attr('class').replace('.', '');
                            $(buttonId).trigger('click');
                        }
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
         * Get the logger modal popup.
         */
        getLoggerModal: function () {
            var self = this;
            var title = this.o.jsConfig.module.title + ' ' + __('Logger');
            ConfirmModal({
                title: title,
                innerScroll: true,
                modalClass: 'nbn-modal',
                content: AipTemplate.getLogger({}),
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
