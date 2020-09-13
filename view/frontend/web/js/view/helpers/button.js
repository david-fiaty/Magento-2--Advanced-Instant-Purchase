define([
    'jquery',
    'mage/translate',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/slider',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/util',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/message',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/validation',
], function ($, __, AdditionalValidators, AipSlider, AipUtil, AipMessage, AipValidation) {
    'use strict';

    AdditionalValidators.registerValidator(AipValidation);

    return {
        cancelButtonSelector: '.action-close',
        buttonClasses: 'action-secondary action-dismiss',

        /**
         * Update the button states.
         */
        update(updateUi) {
            AdditionalValidators.updateUi = updateUi || true;
            if (AdditionalValidators.validate()) {
                $('.aip-submit').prop('disabled', false);
            }
            else {
                $('.aip-submit').prop('disabled', true);
            }
        },
        
        /**
         * Set the additional validator events.
         */
        setValidationEvents() {
            var self = this;
            $('.aip-select, .aip-box').on('change', function() {
                self.update();
            });
        },
        
        /**
         * Get the modal cancel button.
         */
        getCancel: function(obj) {
            var self = this;
            return {
                text: __('Cancel'),
                class: self.buttonClasses,
                click: function(e) {
                    if (obj.isSubView) {
                        // Toggle the view
                        AipSlider.toggleView(obj, e); 
                        obj.getConfirmContent();
                    }
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
                class: 'action-primary action-accept aip-submit',
                click: function(e) {
                    if (AdditionalValidators.validate()) {
                        var requestData = AipUtil.getCurrentForm(obj.isSubView).serialize();
                        AipSlider.showLoader(obj);
                        $.ajax({
                            cache: false,
                            url: AipUtil.getConfirmUrl(obj.isSubView),
                            data: requestData,
                            type: 'post',
                            dataType: 'json',
                            success: function(data) {
                                AipMessage.checkResponse(data, e, obj);
                            },
                            error: function(request, status, error) {
                                obj.log(error);
                            }
                        });
                    }
                    else {
                        AipMessage.show(
                            'error',
                            __('Please approve the terms and conditions.'),
                            obj
                        );
                    }
                }
            };
        },

        /**
         * Enable the submit button.
         */
        allowSubmit: function() {

        }
    };
});
