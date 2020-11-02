define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/confirm',
    'Naxero_BuyNow/js/view/helpers/template',
    'Naxero_BuyNow/js/view/helpers/slider',
    'Naxero_BuyNow/js/view/helpers/util',
    'Naxero_BuyNow/js/view/helpers/message',
    'Naxero_BuyNow/js/view/helpers/logger'
], function($, __, ConfirmModal, AipTemplate, AipSlider, AipUtil, AipMessage, AipLogger) {
    'use strict';

    return {
        modalWrapperSelector: '.modal-inner-wrap',
        submitButtonSelector: '.aip-submit',
        submitButtonClasses: 'action-primary action-accept aip-submit',
        cancelButtonSelector: '.action-close',
        cancelButtonClasses: 'action-secondary action-dismiss',

        /**
         * Initialise the object.
         */
        init: function(obj) {
            this.o = obj;
            return this;
        },

        /**
         * Add HTML to a container.
         */
        addHtml: function(target, html) {
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
        getOrderModal: function(obj) {
            var self = this;
            ConfirmModal({
                title: this.o.jsConfig.popups.popup_title,
                innerScroll: true,
                modalClass: 'aip-modal',
                content: AipTemplate.getConfirmation({}),
                buttons: [{
                    text: __('Cancel'),
                    class: self.cancelButtonClasses,
                    click: function(e) {
                        $(self.cancelButtonSelector).trigger('click');
                    }
                },
                {
                    text: obj.jsConfig.popups.popup_confirm_button_text,
                    class: this.submitButtonClasses,
                    click: function(e) {
                        AipSlider.showLoader();
                        var requestData = AipUtil.getCurrentFormData();
                        $.ajax({
                            cache: false,
                            url: self.getConfirmUrl(obj),
                            data: requestData,
                            type: 'post',
                            dataType: 'json',
                            success: function(data) {
                                AipMessage.checkResponse(data, e);
                            },
                            error: function(request, status, error) {
                                AipLogger.log(
                                    __('Error submitting the form data'),
                                    error
                                );
                            }
                        });
                    }
                }]
            });
        },

    };
});
