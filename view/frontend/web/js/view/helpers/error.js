/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/template',
    'text!Naxero_AdvancedInstantPurchase/template/messages.html',
], function ($, MageTemplate, MessagesTemplate) {
    'use strict';

    return {
        clearErrors: function(slide) {
            slide.find('.messages').remove();
            slide.find('input').removeClass('mage-error');
            slide.find('div.mage-error').remove();
        },
        checkResponse: function(data, slide) {
            var cssClass;
            this.clearErrors(slide);
            slide.prepend(
                MageTemplate(MessagesTemplate)({})
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
            else {
                slide.find('.message').addClass('success');
                slide.find('.message-text').text(data.messages.main);
                slide.find('.messages').show();
            }
        }
    };
});
