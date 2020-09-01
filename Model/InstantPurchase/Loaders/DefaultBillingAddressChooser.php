<?php
namespace Naxero\AdvancedInstantPurchase\Model\InstantPurchase\Loaders;

use Magento\Customer\Model\Customer;

/**
 * Billing address chooser implementation to choose customer default billing address.
 */
class DefaultBillingAddressChooser implements \Magento\InstantPurchase\Model\BillingAddressChoose\BillingAddressChooserInterface
{
    /**
     * @inheritdoc
     */
    public function choose(Customer $customer)
    {
        $address = $customer->getDefaultBillingAddress();
        return $address ?: null;
    }
}
