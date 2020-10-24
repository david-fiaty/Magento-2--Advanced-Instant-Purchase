/**
 * Naxero.com
 * Professional ecommerce integrations for Magento
 *
 * PHP version 7
 *
 * @category  Magento2
 * @package   Naxero
 * @author    Platforms Development Team <contact@naxero.com>
 * @copyright Naxero.com
 * @license   https://opensource.org/licenses/mit-license.html MIT License
 * @link      https://www.naxero.com
 */

 var config = {
    paths: {
        select2: 'Naxero_AdvancedInstantPurchase/js/lib/select2/select2.full',
        slick: 'Naxero_AdvancedInstantPurchase/js/lib/slick/slick',
        popover: 'Naxero_AdvancedInstantPurchase/js/lib/popover/popover',
        aip: 'Naxero_AdvancedInstantPurchase/js/view/instant-purchase',
        aipLogger: 'Naxero_AdvancedInstantPurchase/js/view/helper/logger'
    },
    urlArgs: "bust=" + (new Date()).getTime()
};