define([
    'jquery',
    'mage/translate',
    'mage/template',
    'Magento_Ui/js/modal/confirm',
    'text!Naxero_AdvancedInstantPurchase/template/confirmation.html',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/button'
], function ($, __, MageTemplate, ConfirmModal, ConfirmationTemplate, AiiButton) {
    'use strict';

    return {
        aiiConfig: window.advancedInstantPurchase,
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
                responsive: this.aiiConfig.display.popup_responsive,
                content: confirmTemplate({
                    data: confirmData
                }),
                buttons: [
                    AiiButton.getCancel(obj),
                    AiiButton.getSubmit(obj)
                ]
            });
        }
    };
});
