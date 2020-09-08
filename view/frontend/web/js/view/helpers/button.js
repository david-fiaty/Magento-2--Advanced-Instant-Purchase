define([
    'jquery',
    'mage/translate',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/slider',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/util',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/message',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/validation',
], function ($, __, AdditionalValidators, AiiSlider, AiiUtil, AiiMessage, AiiValidation) {
    'use strict';

    AdditionalValidators.registerValidator(AiiValidation);

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
            return {
                text: __('Submit'),
                class: 'action-primary action-accept',
                click: function(e) {
                    if (AdditionalValidators.validate()) {
                        var requestData = AiiUtil.getCurrentForm(obj.isSubView).serialize();
                        AiiSlider.showLoader(obj);
                        $.ajax({
                            cache: false,
                            url: AiiUtil.getConfirmUrl(obj.isSubView),
                            data: requestData,
                            type: 'post',
                            dataType: 'json',
                            success: function(data) {
                                AiiMessage.checkResponse(data, e, obj);
                            },
                            error: function(request, status, error) {
                                obj.log(error);
                            }
                        });
                    }
                }
            };
        }
    };
});
