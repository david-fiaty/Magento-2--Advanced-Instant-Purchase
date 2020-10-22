define([
    'jquery',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/slider'
], function ($, AipSlider) {
    'use strict';

    return {
        cancelButtonSelector: '.action-close',
        clearErrors: function(slide) {
            slide.find('.messages').remove();
            slide.find('input').removeClass('mage-error');
            slide.find('div.mage-error').remove();
        },

        checkResponse: function(data, e, obj) {
            var cssClass;
            if (data.success === false) {
                // Add the main message
                cssClass = 'mage-error';
                this.show('error', data.messages.main, obj);

                // Add the field messages
                if (data.messages.fields.length > 0) {
                    for (var i = 0; i < data.messages.fields.length; i++) {
                        let err = data.messages.fields[i];
                        let item = "input[name='" + err.id + "'],";
                        item += " input[name='" + err.id + "[]']";
                        $('<div generated="true" class="' + cssClass + '">' + err.txt + '</div>').insertAfter(item);
                        $(item).addClass(cssClass);
                    }
                }
            }
            else if (data.hasOwnProperty('response')) {
                $(this.cancelButtonSelector).trigger('click');
            }
            else {
                this.show('success', data.messages.main, obj);
            }
        },

        show: function(type, str, obj) {
            var slide = AipSlider.getCurrentSlide(obj);
            this.clearErrors(slide);
            slide.prepend(obj.loader);
            slide.find('.message').addClass(type);
            slide.find('.message-text').text(str);
            slide.find('.messages').show();
        }
    };
});
