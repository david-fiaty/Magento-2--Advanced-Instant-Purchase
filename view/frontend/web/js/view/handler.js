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
    'uiComponent',
    'Magento_Ui/js/modal/confirm',
    'Naxero_BuyNow/js/view/core',
    'Naxero_BuyNow/js/view/helpers/logger',
    'Naxero_BuyNow/js/view/helpers/select',
    'Naxero_BuyNow/js/view/helpers/product',
    'Naxero_BuyNow/js/view/helpers/view',
    'Naxero_BuyNow/js/view/helpers/paths',
    'Naxero_BuyNow/js/view/helpers/login',
    'Naxero_BuyNow/js/view/helpers/tree',
    'Naxero_BuyNow/js/view/helpers/template',
    'Naxero_BuyNow/js/view/helpers/gallery',
    'Naxero_BuyNow/js/view/helpers/message',
    'mage/validation',
    'mage/cookies',
    'elevatezoom',
    'domReady!'
], function ($, __, Component, ConfirmModal, NbnCore, NbnLogger, NbnSelect, NbnProduct, NbnView, NbnPaths, NbnLogin, NbnTree, NbnTemplate, NbnGallery, NbnMessage) {
    'use strict';

    return Component.extend({
        /**
         * Default parameters.
         */
        defaults: {
            helpers: arguments,
            config: {},
            slider: {},
            uuid: null,
            showButton: false,
            loggerUrl: 'logs/index',
            galleryUrl: 'product/gallery',
            confirmationUrl: 'order/confirmation',
            buttonContainerSelector: '.nbn-button-container',
            popupContentSelector: '#nbn-confirmation-content',
            logViewerButtonSelector: '#nbn-ui-logger-button',
            formKeySelectorPrefix: '#nbn-form-key-',
            buttonSelectorPrefix: '#nbn-button-',
            buttonSelector: '.nbn-button',
            isSubView: false,
            loader: '',
            modalWrapperSelector: '.modal-inner-wrap',
            submitButtonSelector: '.nbn-submit',
            submitButtonClasses: 'action-primary action-accept nbn-submit',
            cancelButtonSelector: '.action-close',
            cancelButtonSelectorPrefix: '.nbn-button-',
            orderUrl: 'order/request',
            confirmationData: {
                message: __('Are you sure you want to place order and pay?'),
                shippingAddressTitle: __('Shipping Address'),
                billingAddressTitle: __('Billing Address'),
                paymentMethodTitle: __('Payment Method'),
                shippingMethodTitle: __('Shipping Method')
            }
        },

        /** @inheritdoc */
        initialize: function () {
            this._super();

            // Load a button instance
            NbnCore.load(this.config);

            // Attributes and options validation
            NNbnProduct.initValidation(this.config.product.id);

            // Widget features
            if (NbnView.isWidgetView()) {
                this.handleImageClick();
            }

            // Button click event
            this.handleButtonClick();

            // Log the step
            NbnLogger.log(
                __('Configuration loaded for product id %1').replace(
                    '%1',
                    window.naxero.nbn.current.product.id
                ),
                this.config
            );
        },

        /**
         * Add HTML to a container.
         */
        addHtml: function (target, html) {
            $(target).html(html);
            $(this.modalWrapperSelector).animate(
                {minHeight: $(target).height()  + 'px'},
                300
            );
        },

        /**
         * Build a product gallery.
         */
        getGalleryData: function (e) {
            // Prepare variables
            var self = this;
            var productId = $(e.currentTarget).data('product-id');
            var params = {
                product_id: productId,
                form_key: $(this.formKeySelectorPrefix + productId).val()
            };

            // Set the data viewer button event
            $.ajax({
                type: 'POST',
                cache: false,
                url: NbnPaths.get(self.galleryUrl),
                data: params,
                success: function (data) {
                    // Get the HTML content
                    self.addHtml(self.popupContentSelector, data.html);

                    // Build the gallery
                    NbnGallery.build();
                },
                error: function (request, status, error) {
                    NbnLogger.log(
                        __('Error retrieving the product gallery data'),
                        error
                    );
                }
            });
        },

        /**
         * Build a browsable tree with log data.
         */
        getLoggerData: function (e) {
            // Prepare variables
            var self = this;
            var productId = $(e.currentTarget).data('product-id');
            var params = {
                product_id: productId,
                form_key: $(this.formKeySelectorPrefix + productId).val()
            };

            // Set the data viewer button event
            $.ajax({
                type: 'POST',
                cache: false,
                url: NbnPaths.get(self.loggerUrl),
                data: params,
                success: function (data) {
                    // Get the HTML content
                    self.addHtml(self.popupContentSelector, data.html);
                  
                    // Build the data tree
                    NbnTree.build(productId);
                },
                error: function (request, status, error) {
                    NbnLogger.log(
                        __('Error retrieving the UI logging data'),
                        error
                    );
                }
            });
        },

        /**
         * Handle the image click event.
         */
        handleImageClick: function () {
            // Prepare variables
            var self = this;

            // Selectors
            var boxId = '#nbn-widget-product-box-' + this.config.product.id;
            var imageContainer = boxId + ' .nbn-product-box-image';
            var image = imageContainer + ' img';

            // Zoom parameters
            var zoomType = this.config.widgets.widget_zoom_type;
            var isLightbox = this.config.widgets.widget_zoom_type == 'lightbox';
            var params = {
                responsive: true,
                zoomType: zoomType
            };

            // Image initial state
            if (!isLightbox) {
                // Zoom initialisation
                $(image).elevateZoom(params);
            } else {
                // Image state
                $(imageContainer).css('cursor', 'zoom-in');
            }

            // Image container click event
            $(imageContainer).on('click touch', function (e) {
                if (isLightbox) {
                    // Image state
                    $(this).css('cursor', 'zoom-in');

                    // Open the modal
                    self.getGalleryModal(e);

                    // Get the log data
                    self.getGalleryData(e);
                }
            });
        },

        /**
         * Handle the button click event.
         */
        handleButtonClick: function () {
            // Prepare variables
            var self = this;
            var button = $(this.buttonSelectorPrefix + this.config.product.id);

            // Enable the buy now button
            button.prop('disabled', false);

            // Button click event
            button.on('click touch', function (e) {
                if (e.target.nodeName == 'BUTTON') {
                    // Force Login
                    if (!NbnLogin.isLoggedIn()) {
                        NbnLogin.loginPopup();
                        return;
                    }

                    // Validate the product options if needed
                    var productId = $(this).data('product-id');
                    if (!NbnProduct.validateFields(productId)) {
                        // Display the errors
                        NbnProduct.clearErrors(e);
                        NbnProduct.displayErrors(e);
                        return;
                    }

                    // Page view and/or all conditions valid
                    self.purchasePopup(e);
                } else if (e.target.nodeName == 'A') {
                    // Open the modal
                    self.getLoggerModal(e);

                    // Get the log data
                    self.getLoggerData(e);
                }
            });
        },

        /**
         * Get the confirmation page content.
         */
        getConfirmContent: function (e) {
            // Prepare the parameters
            var self = this;
            var productId = $(e.currentTarget).data('product-id');
            var formKey = $(this.formKeySelectorPrefix + productId).val();
            var productQuantity = parseInt($(e.currentTarget).parents().find('.nbn-qty').val());
            var params = {
                product_id: productId,
                form_key: formKey,
                product_quantity: productQuantity
            };

            // Log the parameters
            NbnLogger.log(
                __('Confirmation window request parameters'),
                params
            );

            // Send the request
            $.ajax({
                type: 'POST',
                cache: false,
                url: NbnPaths.get(this.confirmationUrl),
                data: params,
                success: function (data) {
                    // Get the HTML content
                    self.addHtml(self.popupContentSelector, data.html);

                    // Update the selected product options values
                    NbnProduct.updateSelectedAttributesValues(productId);

                    // Initialise the select lists
                    NbnSelect.build(self);
                },
                error: function (request, status, error) {
                    NbnLogger.log(
                        __('Error retrieving the confimation window data'),
                        error
                    );
                }
            });
        },

        /**
         * Purchase popup.
         */
        purchasePopup: function (e) {
            // Get the current form
            var form = NbnProduct.getProductForm();

            // Check the validation rules
            var condition1 = form.validation() && form.validation('isValid');
            if (!condition1) {
                return;
            }

            // Open the modal
            this.getOrderModal(e.currentTarget);

            // Get the AJAX content
            this.getConfirmContent(e);
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
        },

        /**
         * Get the confirmation page modal popup.
         */
        getOrderModal: function (currentTarget) {
            // Prepare variables
            var self = this;
            var productId = $(currentTarget).data('product-id');
            var config = window.naxero.nbn.instances[productId];

            // Load the modal
            ConfirmModal({
                title: config.popups.popup_title,
                innerScroll: true,
                modalClass: 'nbn-modal',
                content: NbnTemplate.getConfirmation({}),
                buttons: [{
                    text: __('Cancel'),
                    class: self.cancelButtonSelectorPrefix + config.product.id,
                    click: function (e) {
                        if (self.isSubView) {
                            self.slider.toggleView(e);
                        }
                        else {
                            $(self.cancelButtonSelector).trigger('click');
                        }
                    }
                },
                {
                    text: config.popups.popup_confirm_button_text,
                    class: self.submitButtonClasses,
                    click: function (e) {
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
                }]
            });
        }
    });
});
