define([
    'jquery',
    'mage/translate',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/slider',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/util',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/message'
], function ($, __, AiiSlider, AiiUtil, AiiMessage) {
    'use strict';

    return {
        cancelButtonSelector: '.action-close',

        /**
         * Get the modal cancel button.
         */
        getCancel: function(obj) {
            var self = this;
            return {
                text: __('Cancel'),
                class: 'action-secondary action-dismiss',
                click: function(e) {
                    AiiSlider.showLoader(obj);
                    if (obj.isSubView) {
                        AiiSlider.toggleView(e, obj);                        }
                    else {
                        $(self.cancelButtonSelector).trigger('click');
                    }
                }
            }
        },

        /**
         * Get the modal submit button.
         */
        getSubmit: function(obj) {
            var params = AiiUtil.getCurrentForm(obj.isSubView);
            console.log(params);
           // $(this).data('field')
            return {
                text: __('Submit'),
                class: 'action-primary action-accept',
                click: function(e) {
                    AiiSlider.showLoader(obj);
                    var request = {
                        url: AiiUtil.getConfirmUrl(obj.isSubView),
                        data: AiiUtil.getCurrentForm(obj.isSubView).serialize(),
                        type: 'post',
                        dataType: 'json',
                        success: function(data) {
                            AiiMessage.checkResponse(data, e, obj);
                        },
                        error: function(request, status, error) {
                            obj.log(error);
                        }
                    };

                    console.log(request);

                    $.ajax(request);
                }
            };
        }
    };
});
