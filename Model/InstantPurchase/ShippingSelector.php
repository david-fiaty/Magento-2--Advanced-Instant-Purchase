<?php
namespace Naxero\AdvancedInstantPurchase\Model\InstantPurchase;

/**
 * Class ShippingSelector.
 */
class ShippingSelector
{
    /**
     * @var Shipping
     */
    public $shippingModel;

    /**
     * ShippingSelector constructor.
     */
    public function __construct(
        \Magento\Shipping\Model\Shipping $shippingModel
    ) {
        $this->shippingModel = $shippingModel;
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
     * Gets all shipping rates avaiable.
     *
     * @param Customer $customer
     * @return Array
     */
    public function getShippingRates($customer)
    {
        // Get the default shipping address
        $address = $customer->getDefaultShippingAddress();
        
        // Collect the shipping rates
        $shippingRates = $address->collectRatesByAddress($address);

        // Format the data
        $output = [];
        foreach ($shippingRates as $rate) {
            $output[] = $rate->toArray();
        }

        return $output;
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
