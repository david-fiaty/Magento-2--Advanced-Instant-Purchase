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

namespace Naxero\BuyNow\Model\Config\Backend\Source;

/**
 * Class ProductTreeFilter.
 */
class ProductTreeFilter implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'parent',
                'label' => __('Parent products only')
            ],
            [
                'value' => 'child',
                'label' => __('Children products only')
            ],
            [
                'value' => 'all',
                'label' => __('All products')
            ]
        ];
    }
}
