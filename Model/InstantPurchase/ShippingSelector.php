<?php
namespace Naxero\AdvancedInstantPurchase\Model\InstantPurchase;

/**
 * Class ShippingSelector.
 */
class ShippingSelector
{
    /**
     * @var Config
     */
    public $shippingModel;

    /**
     * @var Config
     */
    public $configHelper;

    /**
     * ShippingSelector constructor.
     */
    public function __construct(
        \Magento\Shipping\Model\Config $shippingModel,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper
    ) {
        $this->shippingModel = $shippingModel;
        $this->configHelper = $configHelper;
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
    public function getShippingRates($customer)
    {
        $carriers = $this->shippingModel->getActiveCarriers();
        $methods = [];
        foreach ($carriers as $shippingCode => $shippingModel)
        {
            $carrierMethods = $shippingModel->getAllowedMethods();
            if ($carrierMethods) {
                foreach ($carrierMethods as $methodCode => $method) {
                    // Get the carrier price
                    $carrierPrice = $this->configHelper->value(
                        'carriers/'. $shippingCode . '/price',
                        true
                    );

                    // If the carrier has a price
                    if ($carrierPrice) {
                        $code = $shippingCode . '_' . $methodCode;
                        $carrierTitle = $this->configHelper->value(
                            'carriers/'. $shippingCode . '/title',
                            true
                        );
                        $methods[] = [
                            'value' => $code,
                            'label'=> $carrierTitle,
                            'price' => $carrierPrice
                        ];
                    }
                }
            }
       }

       return $methods;
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
