<?php
namespace Naxero\AdvancedInstantPurchase\Model\InstantPurchase\Loaders;

use Magento\Customer\Model\Customer;

/**
 * Shipping address chooser implementation to choose customer default shipping address.
 */
class DefaultShippingAddressChooser implements \Magento\InstantPurchase\Model\ShippingAddressChoose\ShippingAddressChooserInterface
{
    /**
     * @inheritdoc
     */
    public function choose(Customer $customer)
    {
        $address = $customer->getDefaultShippingAddress();
        return $address ?: null;
    }
}
