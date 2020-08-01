<?php
namespace Naxero\AdvancedInstantPurchase\Model\InstantPurchase;

/**
 * Class ShippingSelector.
 */
class ShippingSelector
{
    /**
     * @var AddressFactory
     */
    public $addressFactory;
 
    /**
     * Class ShippingSelector constructor.
     */
    public function __construct(
        \Magento\Customer\Model\AddressFactory $addressFactory
    )
    {
        $this->addressFactory = $addressFactory;
    }

    /**
     * Selects a shipping method.
     *
     * @param Address $address
     * @return Rate
     */
    public function getShippingMethod($address)
    {
        $address->setCollectShippingRates(true);
        $address->collectShippingRates();
        $shippingRates = $address->getAllShippingRates();

        if (empty($shippingRates)) {
            return null;
        }

        $cheapestRate = $this->selectCheapestRate($shippingRates);
        return $cheapestRate->getCode();
    }

    /**
     * Gets all shipping methods avaiable.
     *
     * @param Customer $customer
     * @return Array
     */
    public function getShippingMethods($customer)
    {
        // Get the default shipping address
        $shippingAddressId = $customer->getDefaultShipping();
        $address = $this->addressFactory->create()->load($shippingAddressId);

        // Collect the shipping rates
        $address->setCollectShippingRates(true);
        $address->collectShippingRates();
        $shippingRates = $address->getAllShippingRates();

        return $shippingRates;
    }

    /**
     * Selects shipping price with minimal price.
     *
     * @param Rate[] $shippingRates
     * @return Rate
     */
    private function selectCheapestRate(array $shippingRates)
    {
        $rate = array_shift($shippingRates);
        foreach ($shippingRates as $tmpRate) {
            if ($tmpRate->getPrice() < $rate->getPrice()) {
                $rate = $tmpRate;
            }
        }

        return $rate;
    }
}
