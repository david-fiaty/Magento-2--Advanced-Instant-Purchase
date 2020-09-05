define([
    'jquery',
    'mage/translate',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/slider',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/util',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/message'
], function ($, __, AiiSlider, AiiUtil, AiiMessage) {
    'use strict';

    return {
        /**
         * Get the modal cancel button.
         */
        getCancel: function(modal, obj) {
            return {
                text: __('Cancel'),
                class: 'action-secondary action-dismiss',
                click: function(e) {
                    if (obj.isSubView) {
                        AiiSlider.toggleView(e, obj);                        }
                    else {
                        modal.closeModal(e);
                    }
                }
            }
        },

        /**
         * Get the modal submit button.
         */
        getSubmit: function(modal, obj) {
            return {
                text: __('Submit'),
                class: 'action-primary action-accept',
                click: function(e) {
                    $.ajax({
                        url: AiiUtil.getConfirmUrl(obj.isSubView),
                        data: AiiUtil.getCurrentForm(obj.isSubView).serialize(),
                        type: 'post',
                        dataType: 'json',
                        success: function(data) {
                            AiiMessage.checkResponse(data, obj);
                            //modal.closeModal(e);
                        },
                        error: function(request, status, error) {
                            obj.log(error);
                        }
                    })
                }
            };
        }
    };
});
