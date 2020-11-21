<?php
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

namespace Naxero\BuyNow\Model\Config;

/**
 * Class Naming.
 */
class Naming
{
    /**
     * Get the module name.
     */
    public static function getModuleName()
    {
        return 'Naxero_BuyNow';
    }

    /**
     * Get the module alias.
     */
    public static function getModuleAlias()
    {
        return 'naxero_buy_now';
    }

    /**
     * Get the module path.
     */
    public static function getModulePath()
    {
        return 'Naxero\BuyNow';
    }

    /**
     * Get the module route.
     */
    public static function getModuleRoute()
    {
        return 'naxero-buynow';
    }

    /**
     * Get the module title.
     */
    public static function getModuleTitle()
    {
        return __('Naxero Buy Now');
    }
}
