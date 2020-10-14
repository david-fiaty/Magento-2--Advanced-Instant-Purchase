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
        aipConfig: window.advancedInstantPurchase,
        submitButtonSelector: '.aip-submit',
        submitButtonClasses: 'action-primary action-accept aip-submit',
        cancelButtonSelector: '.action-close',
        cancelButtonClasses: 'action-secondary action-dismiss',

        /**
         * Initialise the button states.
         */
        init() {
            $(this.submitButtonSelector).prop(
                'disabled',
                !AdditionalValidators.validate(true)
            );
        },

        /**
         * Update the button states.
         */
        update() {
            $(this.submitButtonSelector).prop(
                'disabled',
                !AdditionalValidators.validate()
            );
        },
        
        /**
         * Set the additional validator events.
         */
        setValidationEvents() {
            var self = this;

            // Set the button states
            self.init();

            // Fields value change event
            $(AipValidation.inputSelectors).on('change', function() {
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
                class: self.cancelButtonClasses,
                click: function(e) {
                    if (obj.isSubView) {
                        // Toggle the view
                        AipSlider.toggleView(obj, e); 
                        obj.getConfirmContent(obj, e);
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
            var self = this;
            return {
                text: __('Submit'),
                class: self.submitButtonClasses,
                click: function(e) {
                    if (AdditionalValidators.validate()) {
                        AipSlider.showLoader(obj);
                        var requestData = AipUtil.getCurrentForm(obj).serialize();
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
        }
    };
});
