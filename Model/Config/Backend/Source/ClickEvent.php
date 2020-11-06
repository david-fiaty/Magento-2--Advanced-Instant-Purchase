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
 * Class ClickEvent
 */
class ClickEvent implements \Magento\Framework\Option\ArrayInterface
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
                'value' => 'popup',
                'label' => __('Open the login popup')
            ],
            [
                'value' => 'redirect',
                'label' => __('Redirect to the login page')
            ]
        ];
    }
}
