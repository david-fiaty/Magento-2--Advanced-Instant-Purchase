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
 * Class ProductsWithOptions
 */
class ProductsWithOptions implements \Magento\Framework\Option\ArrayInterface
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
                'value' => 'open',
                'label' => __('Open the popup with options to select')
            ],
            [
                'value' => 'validate',
                'label' => __('Force the user to select options')
            ],
            [
                'value' => 'redirect',
                'label' => __('Redirect the user to the product view page')
            ]
        ];
    }
}
