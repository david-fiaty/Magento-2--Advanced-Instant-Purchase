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

define([
    'jquery',
    'Naxero_BuyNow/js/view/helpers/modal',
    'elevatezoom',
    'domReady!'
], function ($, NbnModal, elevateZoom) {
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
         * Build the widget features.
         */
        build: function (obj) {
            // Zoom parameters
            var boxId = '#nbn-product-box-' + this.o.jsConfig.product.id;
            var zoomType = this.o.jsConfig.widgets.widget_zoom_type;
            var isLightbox = this.o.jsConfig.widgets.widget_zoom_type == 'lightbox';
            var params = {
                responsive: true,
                zoomType: zoomType
            };

            // Zoom initialisation
            if (!isLightbox) {
                $(boxId + ' .nbn-product-box-image img').elevateZoom(params); 
            }
            else {
                $(boxId + ' .nbn-product-box-image').css('cursor', 'zoom-in'); 
                $(boxId + ' .nbn-product-box-image').on('click touch', function() {
                    NbnModal.getGalleryModal(obj);
                });           
            }
        }
    };
});
