define([
    'jquery',
    'mage/translate',
    'slick',
], function ($, __, slick) {
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
                speed: 500,
                arrows: false
            });
        },

        /**
         * Get the current slide.
         */
        getCurrentSlide: function(obj) {
            var slide = (obj.isSubView)
            ? this.nextSlideSelector
            : obj.popupContentSelector;

            return $(slide);
        },

        /**
         * Show the AJAX loader.
         */
        showLoader: function(obj) {
            this.getCurrentSlide(obj).html(obj.loader);
        },

        /**
         * Handles the view switch.
         */
        toggleView: function(obj, e) {
            // Handle the event
            e = e || null;
            if (e) e.preventDefault();

            // Handle the toggle logic
            this.showLoader(obj);
            if (obj.isSubView) {
                $(this.sliderSelector).slick('slickPrev');
                obj.isSubView = false;
                $('.action-dismiss span').text(__('Cancel'));
                $(this.sliderSelector).slick('unslick');
            }
            else {
                $(this.sliderSelector).slick('slickNext');
                $('.action-dismiss span').text(__('Back'));
                obj.isSubView = true;
            }
        }
    };
});
