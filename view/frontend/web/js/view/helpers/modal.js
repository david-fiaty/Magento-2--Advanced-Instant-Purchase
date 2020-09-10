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

        /**
         * Get the confirmation page modal popup.
         */
        getConfirmModal: function(confirmData, obj) {
            var self = this;
            var confirmTemplate = MageTemplate(ConfirmationTemplate);
            ConfirmModal({
                title: self.confirmationTitle,
                innerScroll: true,
                data: AipUtil.getCurrentForm(obj.isSubView).serialize(),
                responsive: this.aipConfig.display.popup_responsive,
                content: confirmTemplate({
                    data: confirmData
                }),
                buttons: [
                    AipButton.getCancel(obj),
                    AipButton.getSubmit(obj)
                ]
            });
        }
    };
});
