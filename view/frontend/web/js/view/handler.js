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
    'Naxero_BuyNow/js/view/helpers/logger',
    'Naxero_BuyNow/js/view/helpers/select',
    'Naxero_BuyNow/js/view/helpers/product',
    'Naxero_BuyNow/js/view/helpers/view',
    'Naxero_BuyNow/js/view/helpers/paths',
    'Naxero_BuyNow/js/view/helpers/login',
    'Naxero_BuyNow/js/view/helpers/tree',
    'Naxero_BuyNow/js/view/helpers/template',
    'Naxero_BuyNow/js/view/helpers/message',
    'Naxero_BuyNow/js/view/helpers/util',
    'mage/validation',
    'domReady!'
], function ($, __, Component, ConfirmModal, NbnLogger, NbnSelect, NbnProduct, NbnView, NbnPaths, NbnLogin, NbnTree, NbnTemplate, NbnMessage, NbnUtil) {
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
            this.loadConfig(this.config);

            // Button click event
            this.handleButtonClick();

            // Log the step
            NbnLogger.log(
                __('Configuration loaded for product id %1').replace(
                    '%1',
                    this.config.product.id
                ),
                this.config
            );
        },

        /**
         * Load the current instance config.
         */
        loadConfig: function (config) {
            // Prepare the module js config container
            if (!NbnUtil.has(window, 'naxero.nbn.instances')) {
                window.naxero = {
                    nbn: {
                        instances: {},
                        current: config
                    }
                };
            }

            // Store the current instance config
            window.naxero.nbn.instances[config.product.id] = config;
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
         * Handle the button click event.
         */
        handleButtonClick: function () {
            // Prepare variables
            var self = this;

            // Enable the buy now button
            $(this.buttonSelector).prop('disabled', false);

            // Button click event
            $(this.buttonSelector).off('click touch').on('click touch', function (e) {
                if (e.target.nodeName == 'BUTTON') {
                    // Force Login
                    if (!NbnLogin.isLoggedIn()) {
                        NbnLogin.loginPopup();
                        return;
                    }

                    // Validate the product options if needed
                    var productId = $(e.currentTarget).data('product-id');
                    if (!NbnProduct.validateFields(productId)) {
                        // Display the errors
                        NbnProduct.clearErrors(e);
                        NbnProduct.displayErrors(e);
                        return;
                    }

                    // Page view and/or all conditions valid
                    self.getConfirmationModal(e);
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
        getConfirmationContent: function (e) {
            // Prepare the parameters
            var self = this;
            var productId = $(e.currentTarget).data('product-id');

            // Get the current form
            var params = NbnProduct.getProductFormData(productId);

            // Open the modal
            this.getOrderModal(e.currentTarget);

            // Send the request
            $.ajax({
                type: 'POST',
                cache: false,
                url: NbnPaths.get(this.confirmationUrl),
                data: params,
                success: function (data) {
                    // Get the HTML content
                    self.addHtml(self.popupContentSelector, data.html);

                    // Initialise the select lists
                    NbnSelect.build(productId);
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
         * Order confirmation modal window.
         */
        getConfirmationModal: function (e) {
            // Get the current form
            var form = $(NbnProduct.getProductFormSelector());

            // Check the validation rules
            var condition1 = form.validation() && form.validation('isValid');
            if (!condition1) {
                return;
            }

            // Get the AJAX content
            this.getConfirmationContent(e);
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
                            data: NbnProduct.getOrderFormData(config.product.id),
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
