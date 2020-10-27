define([
    'jquery',
    'mage/translate',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/view',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/slider',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/util',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/message',
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
         * Initialise the button states.
         */
        init(obj) {
            $(this.submitButtonSelector).prop(
                'disabled',
                !AdditionalValidators.validate(obj, true)
            );
        },

        /**
         * Update the button states.
         */
        update(obj) {
            $(this.submitButtonSelector).prop(
                'disabled',
                !AdditionalValidators.validate(obj)
            );
        },

        /**
         * Set the purchase button state.
         */
        setPurchaseButtonState(obj) {
            // Get the button state
            var state = obj.jsConfig.buttons.state_disabled == 1
            && AipView.hasOptions(obj);

            // Apply the state to the button
            $(obj.getButtonId()).prop('disabled', state);
        },
        
        /**
         * Set the additional validator events.
         */
        setValidationEvents(obj) {
            // Set the button states
            this.init(obj);

            // Fields value change event
            var self = this;
            $(AipValidation.inputSelectors).on('change', function() {
                self.update(obj);
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
                    } else {
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
            var submitButton = null;
            if (obj.showSubmitButton) {
                submitButton = {
                    text: __('Submit'),
                    class: self.submitButtonClasses,
                    click: function(e) {
                        if (AdditionalValidators.validate(obj)) {
                            AipSlider.showLoader(obj);
                            var requestData = AipUtil.getCurrentFormData(obj);
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
                                    AipLogger.log(
                                        obj,
                                        __('Error submitting the form data'),
                                        error
                                    );
                                }
                            });
                        } else {
                            AipMessage.show(
                                'error',
                                __('Please approve the terms and conditions.'),
                                obj
                            );
                        }
                    }
                };
            }

            return submitButton;
        }
    };
});
