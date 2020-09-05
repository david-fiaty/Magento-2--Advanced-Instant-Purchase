define([
    'jquery',
    'mage/translate',
    'mage/template',
    'text!Naxero_AdvancedInstantPurchase/template/loader.html',
    'slick',
], function ($, __, MageTemplate, LoaderTemplate, slick) {
    'use strict';

    return {
        sliderSelector: '#aii-slider',
        nextSlideSelector: '#aii-next-slide-container',

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
        toggleView: function(e, obj) {
            e.preventDefault();
            this.showLoader(obj);
            if (obj.isSubView) {
                obj.getConfirmContent();
                $(this.sliderSelector).slick('slickPrev');
                obj.isSubView = false;
                $('.action-dismiss span').text(__('Cancel'));
                $(this.sliderSelector).slick('unslick');
            }
            else {
                $(this.sliderSelector).slick('slickNext');
                $('.action-dismiss span').text(__('Back'));
                obj.getForm(e);
                $(this.nextSlideSelector).show();
                obj.isSubView = true;
            }
        }
    };
});
