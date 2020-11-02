define(
    [
        'mage/template',
        'text!Naxero_BuyNow/template/loader.html',
        'text!Naxero_BuyNow/template/message.html',
        'text!Naxero_BuyNow/template/confirmation.html'
    ],
    function (MageTemplate, Loader, Message, Confirmation) {
        'use strict';

        return {
            /**
             * Initialise the object.
             */
            init: function (obj) {
                this.o = obj;
                return this;
            },

            /**
             * Render the spinner icon template.
             */
            getSpinner: function (params) {
                return MageTemplate(Loader)(params);
            },

            /**
             * Render the UI messages template.
             */
            getMessage: function (params) {
                return MageTemplate(Message)(params);
            },

            /**
             * Render the confirmation modal template.
             */
            getConfirmation: function (params) {
                return MageTemplate(Confirmation)(params);
            }
        };
    }
);