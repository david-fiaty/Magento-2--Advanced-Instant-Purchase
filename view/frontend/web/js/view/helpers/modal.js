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
        aipConfig: window.advancedInstantPurchase,
        confirmationTitle: __('Instant Purchase Confirmation'),
        modalWrapperSelector: '.modal-inner-wrap',

        /**
         * Add HTML to a container.
         */
        addHtml: function(target, html) {
            $(target).html(html);
            $(this.modalWrapperSelector).animate({
                minHeight: $(target).height()  + 'px'
            }, 300 );
        },

        /**
         * Get the confirmation page modal popup.
         */
        build: function(obj) {
            var self = this;
            var confirmTemplate = MageTemplate(ConfirmationTemplate);
            ConfirmModal({
                title: self.confirmationTitle,
                innerScroll: true,
                modalClass: 'aip-modal',
                data: AipUtil.getCurrentForm(obj.isSubView).serialize(),
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
