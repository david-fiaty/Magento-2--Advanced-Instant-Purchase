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
         * Initialise the object.
         */
        init: function (obj) {
            this.o = obj;
            return this;
        },

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
        getOrderModal: function (obj) {
            var self = this;
            ConfirmModal({
                title: obj.jsConfig.popups.popup_title,
                innerScroll: true,
                modalClass: 'nbn-modal',
                content: NbnTemplate.getConfirmation({}),
                buttons: [{
                    text: __('Cancel'),
                    class: self.cancelButtonSelectorPrefix + obj.jsConfig.product.id,
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
                    text: obj.jsConfig.popups.popup_confirm_button_text,
                    class: self.submitButtonClasses,
                    click: function (e) {
                        if (AdditionalValidators.validate()) {
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
                        else {
                            alert('agreements invalid');
                        }
                    }
                }]
            });
        },

        /**
         * Get the logger modal popup.
         */
        getLoggerModal: function (obj) {
            var self = this;
            var title =  obj.jsConfig.popups.popup_title;
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
        getGalleryModal: function (obj) {
            // Prepare parameters
            var self = this;
            var title = obj.jsConfig.product.title;
            var params = {
                data: {
                    product: obj.jsConfig.product
                }
            };

            // Build the modal
            ConfirmModal({
                title: title,
                innerScroll: true,
                modalClass: 'nbn-modal',
                content: NbnTemplate.getGallery(params),
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
