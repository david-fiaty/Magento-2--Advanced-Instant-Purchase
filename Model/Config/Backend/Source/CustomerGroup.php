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
 * Class CustomerGroup
 */
class CustomerGroup implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Customer
     */
    public $customerHelper;

    /**
     * CategoryList constructor.
     */
    public function __construct(
        \Naxero\BuyNow\Helper\Customer $customerHelper
    ) {
        $this->customerHelper = $customerHelper;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->customerHelper->getCustomerGroups();
    }
}
