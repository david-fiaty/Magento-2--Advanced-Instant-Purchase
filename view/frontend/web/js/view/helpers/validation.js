define(
    [
        'jquery',
        'mage/translate'
    ],
    function ($, __) {
        'use strict';
        return {
            aipConfig: window.advancedInstantPurchase,
            agreementRow: '.aip-agreement-link-row',
            updateUi: true;

            /**
             * Additional form validation.
             */
            validate: function () {
                var self = this;
                if (self.aipConfig.general.enable_agreements) {
                    var error = [];
                    $(self.agreementRow).removeClass('error');
                    $(self.agreementRow).each(function(i) {
                        var input = $(this).find('.aip-agreement-box');
                        if (!input.is(':checked')) {
                            error.push(i);
                            if (self.updateUi) {
                                $(this).addClass('error');
                            }
                        }
                    });
                     
                    return error.length == 0;
                }

                return  true;
            }
        }
    }
);