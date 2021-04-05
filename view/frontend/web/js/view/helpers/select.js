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
    'Naxero_BuyNow/js/view/helpers/util',
    'select2'
], function ($, __, NbnUtil, select2) {
    'use strict';

    return {
        listSelector: '.nbn-select',
        linkSelector: '.nbn-new, .nbn-plus-icon',
        paymentMethodSelector: '#nbn-payment-method-code',
        otherMethodsToggleSelector: '#nbn-show-other-methods',
        otherMethodsSelector: '#nbn-other-method-select',

        /**
         * Initialise the select lists.
         */
        build: function (productId) {
            var self = this;

            // Select 2
            $(this.listSelector).select2({
                placeholder: __('Select an option'),
                language: self.getLocale(window.naxero.nbn.instances[productId].user.language),
                theme: 'classic',
                templateResult: NbnUtil.formatIcon,
                templateSelection: NbnUtil.formatIcon
            });
        },

        getLocale: function (code) {
            return code.split('_')[0];
        }
    };
});
