define([
    'jquery',
    'mage/translate',
    'mage/template',
    'text!Naxero_AdvancedInstantPurchase/template/loader.html',
    'slick',
], function ($, __, MageTemplate, LoaderTemplate, slick) {
    'use strict';

    return {
        sliderSelector: '#aip-slider',
        nextSlideSelector: '#aip-next-slide-container',

        /**
         * Create a login popup.
         */
        build: function() {
            $(this.sliderSelector).slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                infinite: false,
                speed: 300,
                adaptiveHeight: true,
                arrows: false
            });
        },

        /**
         * Get the current slide.
         */
        getCurrentSlide: function(obj) {
            var slide = (obj.isSubView) ? this.nextSlideSelector : obj.popupContentSelector;
            return $(slide);
        },

        /**
         * Show the AJAX loader.
         */
        showLoader: function(obj) {
            this.getCurrentSlide(obj).html(
                MageTemplate(LoaderTemplate)({})
            );
        },

        /**
         * Handles the view switch.
         */
        toggleView: function(obj, e) {
            // Handle the event
            e = e || null;
            if (e) e.preventDefault();

            // Handle the toggle logic
            if (obj.isSubView) {
                $(this.sliderSelector).slick('slickPrev');
                this.showLoader(obj);
                obj.isSubView = false;
                $('.action-dismiss span').text(__('Cancel'));
            }
            else {
                $(this.sliderSelector).slick('slickNext');
                this.showLoader(obj);
                $('.action-dismiss span').text(__('Back'));
                obj.isSubView = true;
            }
        }
    };
});
