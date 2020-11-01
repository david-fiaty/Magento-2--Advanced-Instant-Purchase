define([
    'jquery',
    'mage/translate',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/view',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/slider',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/util',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/message',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/validation',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/logger'
], function($, __, AdditionalValidators, AipView, AipSlider, AipUtil, AipMessage, AipValidation, AipLogger) {
    'use strict';

    AdditionalValidators.registerValidator(AipValidation);

    return {
        submitButtonSelector: '.aip-submit',
        submitButtonClasses: 'action-primary action-accept aip-submit',
        cancelButtonSelector: '.action-close',
        cancelButtonClasses: 'action-secondary action-dismiss',

        /**
         * Initialise the object.
         */
        init: function(obj) {
            this.o = obj;
            return this;
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
            // Set the button states
            this.init();

            // Fields value change event
            var self = this;
            $(AipValidation.inputSelectors).on('change', function() {
                self.update();
            });
        },

        /**
         * Get the modal cancel button.
         */
        getCancelButton: function(obj) {
            var self = this;
            return {
                text: __('Cancel'),
                class: self.cancelButtonClasses,
                click: function(e) {
                    if (obj.isSubView) {
                        // Toggle the view
                        AipSlider.toggleView(e);
                        obj.getConfirmContent(e);
                    } else {
                        $(self.cancelButtonSelector).trigger('click');
                    }
                }
            }
        },

        /**
         * Get the modal submit button.
         */
        getSubmitButton: function(obj) {
            var self = this;
            var submitButton = null;
            if (obj.showSubmitButton) {
                submitButton = {
                    text: obj.jsConfig.popups.popup_confirm_button_text,
                    class: this.submitButtonClasses,
                    click: function(e) {
                        if (AdditionalValidators.validate()) {
                            AipSlider.showLoader();
                            var requestData = AipUtil.getCurrentFormData();
                            $.ajax({
                                cache: false,
                                url: AipUtil.getConfirmUrl(),
                                data: requestData,
                                type: 'post',
                                dataType: 'json',
                                success: function(data) {
                                    AipMessage.checkResponse(data, e);
                                },
                                error: function(request, status, error) {
                                    AipLogger.log(
                                        __('Error submitting the form data'),
                                        error
                                    );
                                }
                            });
                        } else {
                            AipMessage.show(
                                'error',
                                __('Please approve the terms and conditions.')
                            );
                        }
                    }
                };
            }

            return submitButton;
        }
    };
});
