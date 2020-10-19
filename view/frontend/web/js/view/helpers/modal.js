define([
    'jquery',
    'mage/translate',
    'mage/template',
    'Magento_Ui/js/modal/confirm',
    'text!Naxero_AdvancedInstantPurchase/template/confirmation.html',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/button',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/util'
], function ($, __, MageTemplate, ConfirmModal, ConfirmationTemplate, AipButton, AipUtil) {
    'use strict';

    return {
        modalWrapperSelector: '.modal-inner-wrap',

        /**
         * Add HTML to a container.
         */
        addHtml: function(target, html) {
            $(target).html(html);
            $(this.modalWrapperSelector).animate(
                {minHeight: $(target).height()  + 'px'}
                , 300
            );
        },

        /**
         * Get the confirmation page modal popup.
         */
        build: function(obj) {
            var confirmTemplate = MageTemplate(ConfirmationTemplate);
            ConfirmModal({
                title: obj.jsConfig.popups.popup_title,
                innerScroll: true,
                modalClass: 'aip-modal',
                content: confirmTemplate({
                    data: {}
                }),
                buttons: [
                    AipButton.getCancel(obj),
                    AipButton.getSubmit(obj)
                ]
            });
        }
    };
});
