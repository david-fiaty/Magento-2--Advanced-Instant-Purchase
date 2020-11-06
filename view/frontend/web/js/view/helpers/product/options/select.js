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
    'Naxero_BuyNow/js/view/helpers/logger',
    'Naxero_BuyNow/js/view/helpers/view',
    'popover',
], function ($, __, NbnLogger, NbnView, popover) {
    'use strict';

    return {
        listProductContainerSelector: '.product-item',
        listProductFormSelector: '.nbn-list-form',
        listProductCartFormSelector: 'form[data-role="tocart-form"]',
        viewProductContainerSelector: '.product-info-main',
        viewProductFormSelector: '#product_addtocart_form',
        productBoxContainerSelector: '.nbn-product-box-container',
        confirmationContainerSelector: '#nbn-confirmation-content',
        optionFieldSelector: '#nbn-option',
        optionSelectorPrefix: '#nbn-option-',
        popoverSelector: '.popover',
        productDataSelectorPrefix: '#nbn-product-data-',
        buttonErrorClass: 'nbn-button-error',

        /**
         * Set product options events.
         */
        initOptionsEvents: function () {
            if (this.hasOptions()) {
                // Prepare the variables
                var options = this.o.jsConfig.product.options;

                // Set the options events and default values
                for (var i = 0; i < options.length; i++) {
                    // Prepare the fields
                    var option = options[i];
                    var sourceField = this.getOptionField(option);

                    // Set the value change events
                    $(sourceField).on('change', function (e) {
                        // Prepare the source Id
                        var sourceId = e.currentTarget;

                        // Prepare the target Id
                        var targetId = '#super_attribute_';
                        targetId += $(this).data('product-id');
                        targetId += '_';
                        targetId += $(this).data('attribute-id');

                        // Assign value from source to target
                        $(targetId).val($(sourceId).val());
                    });
                }
            }
        }
});
