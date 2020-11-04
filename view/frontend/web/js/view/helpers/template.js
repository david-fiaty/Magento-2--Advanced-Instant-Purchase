define(
    [
        'mage/template',
        'text!Naxero_BuyNow/template/loader.html',
        'text!Naxero_BuyNow/template/message.html',
        'text!Naxero_BuyNow/template/slider.html'
    ],
    function (MageTemplate, Loader, Message, Slider) {
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
                return MageTemplate(Slider)(params);
            },

            /**
             * Render the confirmation modal logger.
             */
            getLogger: function (params) {
                return MageTemplate(Slider)(params);
            }
        };
    }
);