define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/template',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/button',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/util'
], function ($, ConfirmModal, AipTemplate, AipButton) {
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
            ConfirmModal({
                title: this.getTitle(obj),
                innerScroll: true,
                modalClass: 'aip-modal',
                content: AipTemplate.getConfirmation({}),
                buttons: this.getButtons(obj)
            });
        },

        /**
         * Get the modal window buttons.
         */
        getButtons: function(obj) {
            return [
                AipButton.getCancel(obj),
                AipButton.getSubmit(obj)
            ];
        },

        /**
         * Get the modal window title.
         */
        getTitle: function(obj) {
            return obj.jsConfig.popups.popup_title;
        }
    };
});
