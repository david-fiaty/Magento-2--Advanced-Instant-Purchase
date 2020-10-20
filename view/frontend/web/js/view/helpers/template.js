define(
    [
        'mage/template',
        'text!Naxero_AdvancedInstantPurchase/template/loader.html',
        'text!Naxero_AdvancedInstantPurchase/template/message.html',
        'text!Naxero_AdvancedInstantPurchase/template/confirmation.html'
    ],
    function (MageTemplate, loader, message, confirmation) {
        'use strict';

        return {
            /**
             * Load and render an HTML template.
             */
            get: function(name, params) {
                return MageTemplate(name)(params);
            }
        };
    }
);