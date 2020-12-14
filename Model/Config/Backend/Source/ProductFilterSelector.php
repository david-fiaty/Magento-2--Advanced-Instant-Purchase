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
 * Class ProductFilterSelector
 */
class ProductFilterSelector implements \Magento\Framework\Option\ArrayInterface
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
                'value' => 'random',
                'label' => __('Random')
            ],
            [
                'value' => 'latest',
                'label' => __('Latest')
            ],
            [
                'value' => 'oldest',
                'label' => __('Oldest')
            ],
            [
                'value' => 'highest_price',
                'label' => __('Highest price')
            ],
            [
                'value' => 'lowest_price',
                'label' => __('Lowest price')
            ],
            [
                'value' => 'lowest_stock',
                'label' => __('Lowest stock')
            ],
            [
                'value' => 'highest_stock',
                'label' => __('Highest stock')
            ],
            [
                'value' => 'lowest_sales',
                'label' => __('Lowest sales')
            ],
            [
                'value' => 'highest_sales',
                'label' => __('Highest sales')
            ]
        ];
    }
}
