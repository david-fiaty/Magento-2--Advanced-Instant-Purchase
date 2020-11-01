define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/template',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/button',
], function($, ConfirmModal, AipTemplate, AipButton) {
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
                ,
                300
            );
        },

        /**
         * Get the confirmation page modal popup.
         */
        build: function() {
            ConfirmModal({
                title: this.o.jsConfig.popups.popup_title,
                innerScroll: true,
                modalClass: 'aip-modal',
                content: AipTemplate.getConfirmation({}),
                buttons: this.getButtons()
            });
        },

        /**
         * Get the modal window buttons.
         */
        getButtons: function() {
            return [
                AipButton.getCancel(),
                AipButton.getSubmit()
            ];
        }
    };
});
