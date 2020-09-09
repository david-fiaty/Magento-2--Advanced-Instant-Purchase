define(
    [
        'jquery',
        'mage/translate'
    ],
    function ($, __) {
        'use strict';
        return {
            aipConfig: window.advancedInstantPurchase,

            /**
             * Additional form validation.
             */
            validate: function () {
                if (this.aipConfig.general.enable_agreements) {
                    return $('#agreement-1').is(":checked");
                }

                return true;
            }
        }
    }
);