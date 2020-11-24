/**
 * Naxero.com
 * Professional ecommerce integrations for Magento.
 *
 * PHP version 7
 *
 * @category  Magento2
 * @package   Naxero
 * @author    Platforms Development Team <contact@naxero.com>
 * @copyright © Naxero.com all rights reserved
 * @license   https://opensource.org/licenses/mit-license.html MIT License
 * @link      https://www.naxero.com
 */

 define(
    [
        'mage/template',
        'text!Naxero_BuyNow/template/loader.html',
        'text!Naxero_BuyNow/template/message.html',
        'text!Naxero_BuyNow/template/widget-gallery.html',
        'text!Naxero_BuyNow/template/slider.html'
    ],
    function (MageTemplate, Loader, Message, WidgetGallery, Slider) {
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
            },

            /**
             * Render the product gallery modal.
             */
            getGallery: function (params) {
                return MageTemplate(WidgetGallery)(params);
            }
        };
    }
);