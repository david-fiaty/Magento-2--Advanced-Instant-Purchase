/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/template',
    'text!Naxero_AdvancedInstantPurchase/template/loader.html',
], function ($, MageTemplate, LoaderTemplate) {
    'use strict';

    return {
        showLoader: function(obj) {
            obj.getCurrentSlide().html(
                MageTemplate(LoaderTemplate)({})
            );
        }
    };
});
