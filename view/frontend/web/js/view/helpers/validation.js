define(
    [
        'jquery',
        'mage/translate'
    ],
    function ($, __) {
        'use strict';
        return {
            aiiConfig: window.advancedInstantPurchase,

            /**
             * Additional form validation.
             */
            validate: function () {
                if (this.aiiConfig.general.enable_agreements) {
                    return $('#agreement-1').is(":checked");
                }

                return true;
            }
        }
    }
);