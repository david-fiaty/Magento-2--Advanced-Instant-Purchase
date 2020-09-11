define(
    [
        'jquery',
        'mage/translate'
    ],
    function ($, __) {
        'use strict';
        return {
            aipConfig: window.advancedInstantPurchase,
            agreementBoxSelector: '.aip-agreement-box',

            /**
             * Additional form validation.
             */
            validate: function () {
                if (this.aipConfig.general.enable_agreements) {
                    var error = [];
                    $(this.agreementBoxSelector).each(function(i) {
                        if (!$(this).is(':checked')) {
                            error.push(i);
                        }
                    });
                     
                    return error.length == 0;
                }

                return true;
            }
        }
    }
);