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

            /**
             * Additional form validation.
             */
            validate: function () {
                if (this.aipConfig.general.enable_agreements) {
                    var error = [];
                    $(this.agreementRow).removeClass('error');
                    $(this.agreementRow).each(function(i) {
                        var input = $(this).find('.aip-agreement-box');
                        if (!input.is(':checked')) {
                            $(this).addClass('error');
                            error.push(i);
                        }
                    });
                     
                    return error.length == 0;
                }

                return  true;
            }
        }
    }
);