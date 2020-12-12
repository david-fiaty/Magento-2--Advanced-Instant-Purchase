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

namespace Naxero\BuyNow\Model\Service;

/**
 * Class FilterHandlerService.
 */
class FilterHandlerService
{
    /**
     * FilterHandlerService constructor.
     */
    public function __construct()
    {
    }

    /**
     * Filter content placeholders.
     */
    public function filterContent($content, $config)
    {
        // Product name
        $content = str_replace('{product_name}', $config['product']['name'], $content);

        // Product price
        $content = str_replace('{product_price}', $config['product']['price'], $content);

        return $content;
    }
}
