define([
    'jquery',
    'mage/template',
    'Naxero_AdvancedInstantPurchase/js/view/helpers/slider',
    'text!Naxero_AdvancedInstantPurchase/template/message.html'
], function ($, MageTemplate, AiiSlider, MessageTemplate) {
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
            var slide = AiiSlider.getCurrentSlide(obj);
            this.clearErrors(slide);
            slide.prepend(
                MageTemplate(MessageTemplate)({})
            );
            if (data.success === false) {
                cssClass = 'mage-error';

                // Add the main message
                slide.find('.message').addClass('error');
                slide.find('.message-text').text(data.messages.main);
                slide.find('.messages').show();

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
                slide = AiiSlider.getCurrentSlide(obj);
                slide.find('.message').addClass('success');
                slide.find('.message-text').text(data.messages.main);
                slide.find('.messages').show();
            }
        }
    };
});
