define(
    [
        'mage/template',
        'text!Naxero_AdvancedInstantPurchase/template/loader.html',
        'text!Naxero_AdvancedInstantPurchase/template/message.html',
        'text!Naxero_AdvancedInstantPurchase/template/confirmation.html'
    ],
    function (MageTemplate, Loader, Message, Confirmation) {
        'use strict';

        return {
            /**
             * Render the loader icon template.
             */
            getLoader: function(params) {
                return MageTemplate(Loader)(params);
            },

            /**
             * Render the UI messages template.
             */
            getMessage: function(params) {
                return MageTemplate(Message)(params);
            },

            /**
             * Render the confirmation modal template.
             */
            getConfirmation: function(params) {
                return MageTemplate(Confirmation)(params);
            }
        };
    }
);