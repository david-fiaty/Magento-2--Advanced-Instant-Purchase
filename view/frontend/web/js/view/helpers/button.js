define([
    'jquery',
    'mage/translate',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Naxero_BuyNow/js/view/helpers/slider',
    'Naxero_BuyNow/js/view/helpers/util',
    'Naxero_BuyNow/js/view/helpers/message',
    'Naxero_BuyNow/js/view/helpers/validation',
    'Naxero_BuyNow/js/view/helpers/logger'
], function($, __, AdditionalValidators, AipSlider, AipUtil, AipMessage, AipValidation, AipLogger) {
    'use strict';

    AdditionalValidators.registerValidator(AipValidation);

    return {

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
         * Get the modal confirmation URL.
         */
        getConfirmUrl: function(obj) {            
            return obj.isSubView 
            ? obj.o.paths.get(obj.saveAddressUrl) 
            : obj.o.paths.get(obj.purchcaseUrl);
        },

        /**
         * Set the additional validator events.
         */
        setValidationEvents() {
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
                                url: self.getConfirmUrl(obj),
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
