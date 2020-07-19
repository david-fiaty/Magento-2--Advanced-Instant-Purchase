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

require(
    [
        'jquery',
        'mage/translate'
    ],
    function ($, __) {
        $(document).ready(function() {
            console.log('mage-cache-storage');
            console.log(window.localStorage.getItem('mage-cache-storage'));

            alert('display.js');
        });
    }
);