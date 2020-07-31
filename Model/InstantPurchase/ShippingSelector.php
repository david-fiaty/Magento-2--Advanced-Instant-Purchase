<?php
namespace Naxero\AdvancedInstantPurchase\Model\InstantPurchase;

/**
 * Class ShippingSelector
 */
class ShippingSelector
{
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
