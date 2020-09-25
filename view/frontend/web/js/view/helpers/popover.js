define([
    'jquery',
    'mage/translate',
    'popover'
], function ($,__) {
    'use strict';

    return {
        /**
         * Product list button error popover.
         */
        getListButtonErrorPopover(button, obj) {
            button.popover({
                title : '',
                content : __('Please select the required options'),
                autoPlace : false,
                trigger : 'hover',
                placement : 'right',
                delay : 10
            });
            button.addClass(obj.buttonErrorClass);
            button.trigger('mouseover');
        },

        /**
         * Product list attribute error popover.
         */
        getListAttributeErrorPopover(productContainer, errors, obj) {
            for (var i = 0; i < errors.length; i++) {
                var attributeContainer = productContainer
                .find('[attribute-id="' + errors[i].id + '"]');
                attributeContainer.css('position', 'relative');
                attributeContainer.append('<span class="aip-attribute-error">&#10006;</span>');
                attributeContainer.find(obj.attributeErrorSelector).popover({
                    title : '',
                    content : __('Required option'),
                    autoPlace : false,
                    trigger : 'hover',
                    placement : 'bottom',
                    delay : 10
                });
            }
        }
    };
});
