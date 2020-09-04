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
        checkResponse: function(data, obj) {
            if (data.success === false) {
                var template = MageTemplate(MessagesTemplate);
                var templateHtml = template({});

                // Add the message
                var slide = obj.getCurrentSlide();
                slide.prepend(templateHtml);
                slide.find('.message').addClass('success');
                slide.find('.message-text').text(data.messages.main);
                slide.find('.messages').show();
            }
        }
    };
});
