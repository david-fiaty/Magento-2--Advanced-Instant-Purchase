define(
    [
        'mage/template',
        'text!Naxero_AdvancedInstantPurchase/template/loader.html',
        'text!Naxero_AdvancedInstantPurchase/template/message.html',
        'text!Naxero_AdvancedInstantPurchase/template/header.html',
        'text!Naxero_AdvancedInstantPurchase/template/confirmation.html'
    ],
    function(MageTemplate, Loader, Message, Header, Confirmation) {
        'use strict';

        return {
            /**
             * Initialise the object.
             */
            init: function() {
                return this;
            },

            /**
             * Render the spinner icon template.
             */
            getSpinner: function(params) {
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
            },

            /**
             * Render the HTML page header template.
             */
            getHeader: function(params) {
                return MageTemplate(Header)(params);
            }
        };
    }
);