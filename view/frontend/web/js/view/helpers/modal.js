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
    'mage/translate',
    'Magento_Ui/js/modal/confirm',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Naxero_BuyNow/js/view/helpers/template',
    'Naxero_BuyNow/js/view/helpers/slider',
    'Naxero_BuyNow/js/view/helpers/product',
    'Naxero_BuyNow/js/view/helpers/logger',
    'Naxero_BuyNow/js/view/helpers/message',
    'Naxero_BuyNow/js/view/helpers/paths',
    'Naxero_BuyNow/js/view/helpers/validation'
], function ($, __, ConfirmModal, AdditionalValidators, NbnTemplate, NbnSlider, NbnProduct, NbnLogger, NbnMessage, NbnPaths, NbnValivation) {
    'use strict';

    // Register the custom validator
    AdditionalValidators.registerValidator(NbnValivation);

    return {
        modalWrapperSelector: '.modal-inner-wrap',
        submitButtonSelector: '.nbn-submit',
        submitButtonClasses: 'action-primary action-accept nbn-submit',
        cancelButtonSelector: '.action-close',
        cancelButtonSelectorPrefix: '.nbn-button-',
        orderUrl: 'order/request',

        /**
         * Add HTML to a container.
         */
        addHtml: function (target, html) {
            $(target).html(html);
            $(this.modalWrapperSelector).animate(
                {minHeight: $(target).height()  + 'px'}
                ,
                300
            );
        },

        /**
         * Get the confirmation page modal popup.
         */
        getOrderModal: function (obj, e) {
            // Prepare variables
            var self = this;
            var productId = $(e.currentTarget).data('product-id');
            var config = window.naxero.nbn.instances[productId];

            // Load the modal
            ConfirmModal({
                title: obj.config.popups.popup_title,
                innerScroll: true,
                modalClass: 'nbn-modal',
                content: NbnTemplate.getConfirmation({}),
                buttons: [{
                    text: __('Cancel'),
                    class: self.cancelButtonSelectorPrefix + obj.config.product.id,
                    click: function (e) {
                        $(self.cancelButtonSelector).trigger('click');
                        if (obj.isSubView) {
                            NbnSlider.toggleView(e);
                            var buttonId = '#' + $(e.currentTarget).attr('class').replace('.', '');
                            $(buttonId).trigger('click');
                        }
                    }
                },
                {
                    text: obj.config.popups.popup_confirm_button_text,
                    class: self.submitButtonClasses,
                    click: function (e) {
                        if (AdditionalValidators.validate(e)) {
                            NbnSlider.showLoader();
                            $.ajax({
                                cache: false,
                                url: NbnPaths.get(self.orderUrl),
                                data: NbnProduct.getProductFormData(),
                                type: 'post',
                                dataType: 'json',
                                success: function (data) {
                                    NbnMessage.checkResponse(data, e);
                                },
                                error: function (request, status, error) {
                                    NbnLogger.log(
                                        __('Error submitting the form data'),
                                        JSON.stringify(error)
                                    );
                                }
                            });
                        }
                    }
                }]
            });
        },

        /**
         * Get the logger modal popup.
         */
        getLoggerModal: function (e) {
            // Prepare parameters
            var self = this;
            var productId = $(e.currentTarget).data('product-id');
            var title = window.naxero.nbn.instances[productId].popups.popup_title;

            // Load the confirm modal
            ConfirmModal({
                title: title,
                innerScroll: true,
                modalClass: 'nbn-modal',
                content: NbnTemplate.getLogger({}),
                buttons: [{
                    text: __('Close'),
                    class: self.cancelButtonClasses,
                    click: function (e) {
                        $(self.cancelButtonSelector).trigger('click');
                    }
                }]
            });
        },

        /**
         * Get the product media gallery modal.
         */
        getGalleryModal: function (e) {
            // Prepare parameters
            var self = this;
            var productId = $(e.currentTarget).data('product-id');
            var title = window.naxero.nbn.instances[productId].product.title;

            // Build the modal
            ConfirmModal({
                title: title,
                innerScroll: true,
                modalClass: 'nbn-modal',
                content: NbnTemplate.getGallery({}),
                buttons: [{
                    text: __('Close'),
                    class: self.cancelButtonClasses,
                    click: function (e) {
                        $(self.cancelButtonSelector).trigger('click');
                    }
                }]
            });
        }
    };
});
