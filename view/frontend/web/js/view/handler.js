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
    'Naxero_BuyNow/js/view/core',
    'mage/validation',
    'mage/cookies',
    'domReady!'
], function ($, __, Component, Core) {
    'use strict';
    
    return Component.extend({
        defaults: {
            jsConfig: {},
            uuid: null,
            showButton: false,
            loggerUrl: 'logs/index',
            confirmationUrl: 'order/confirmation',
            buttonContainerSelector: '.nbn-button-container',
            popupContentSelector: '#nbn-confirmation-content',
            logViewerButtonSelector: '#nbn-ui-logger-button',
            formKeySelectorPrefix: '#nbn-form-key-',
            buttonSelectorPrefix: '#nbn-button-',
            isSubView: false,
            loader: '',
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
            this.o = Core.init(this);
            this.build();
        },

        /**
         * Prepare the purchase data.
         *
         * @param {Object} data
         */
        build: function () {
            var self = this;

            // Spinner icon
            this.o.spinner.loadIcon();

            // Options validation
            this.o.product.initOptionsEvents();

            // Button click event
            self.handleButtonClick();

            // Log the step
            this.o.logger.log(
                __('Configuration loaded for product id %1').replace(
                    '%1',
                    this.jsConfig.product.id
                ),
                this.jsConfig
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
            self.o.slider.showLoader();
            $.ajax({
                type: 'POST',
                cache: false,
                url: self.o.paths.get(self.loggerUrl),
                data: params,
                success: function (data) {
                    // Get the HTML content
                    self.o.modal.addHtml(self.popupContentSelector, data.html);

                    // Build the data tree
                    self.o.tree.build();
                },
                error: function (request, status, error) {
                    self.o.logger.log(
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
            var button = $(this.buttonSelectorPrefix + this.jsConfig.product.id);

            // Enable the buy now button
            button.prop('disabled', false);

            // Button click event
            button.on('click touch', function (e) {
                if (e.target.nodeName == 'BUTTON') {
                    // Force Login
                    if (!self.o.login.isLoggedIn()) {
                        self.o.login.loginPopup();
                        return;
                    }

                    // Validate the product options if needed
                    var optionsValid = self.o.product.validateOptions(e);
                    if (!optionsValid) {
                        // Display the errors
                        self.o.product.clearErrors(e);
                        self.o.product.displayErrors(e);
                        return;
                    }
                    
                    // Page view and/or all conditions valid
                    self.purchasePopup(e);
                }
                else if (e.target.nodeName == 'A') {
                    // Open the modal
                    self.o.modal.getLoggerModal(self);

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
            var params = {
                product_id: productId,
                form_key: $(this.formKeySelectorPrefix + productId).val()
            };

            // Log the parameters
            this.o.logger.log(
                __('Confirmation window request parameters'),
                params
            );

            // Send the request
            this.o.slider.showLoader();
            $.ajax({
                type: 'POST',
                cache: false,
                url: this.o.paths.get(this.confirmationUrl),
                data: params,
                success: function (data) {
                    // Get the HTML content
                    self.o.modal.addHtml(self.popupContentSelector, data.html);

                    // Update the selected product options values
                    self.o.product.updateSelectedOptionsValues(e, self);

                    // Initialise the select lists
                    self.o.select.build();

                    // Agreements events
                    self.o.agreement.build();
                    
                    // Set the slider events
                    self.o.slider.build();

                    // Set the additional validation events
                    self.o.button.setValidationEvents();

                    // Log the purchase data
                    self.o.logger.log(
                        __('Purchase data on page load'),
                        self.o.product.getProductForm().serializeArray()
                    );
                },
                error: function (request, status, error) {
                    self.o.logger.log(
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
            var form = this.o.product.getProductForm();

            // Check the validation rules
            var condition1 = form.validation() && form.validation('isValid');
            if (!condition1) {
                return;
            }

            // Open the modal
            this.o.modal.getOrderModal(this);

            // Get the AJAX content
            this.getConfirmContent(e);
        }
    });
});
