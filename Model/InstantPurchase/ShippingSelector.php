<?php
namespace Naxero\AdvancedInstantPurchase\Model\InstantPurchase;

/**
 * Class ShippingSelector.
 */
class ShippingSelector
{    
    /**
     * @var ShippingMethodInterfaceFactory
     */
    public $shippingMethodFactory;

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
        \Magento\Quote\Api\Data\ShippingMethodInterfaceFactory $shippingMethodFactory,
        \Magento\Shipping\Model\Config $shippingModel,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper
    ) {
        $this->shippingMethodFactory = $shippingMethodFactory;
        $this->shippingModel = $shippingModel;
        $this->configHelper = $configHelper;
    }

    /**
     * Selects a shipping method.
     *
     * @param Address $address
     * @return Rate
     */
    public function getShippingMethod($customer)
    {
        $rates = $this->getShippingRates($customer);
        $shippingMethod = $this->shippingMethodFactory->create()
            ->setCarrierCode($rates['carrier_code'])
            ->setMethodCode($rates['method_code'])
            ->setMethodTitle(__('My method xxx'))
            ->setAvailable(
                $this->areShippingMethodsAvailable(
                    $customer->getDefaultShippingAddress()
                )
            );
            
        return $shippingMethod;
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
                    $carrierPrice = $this->getCarrierPrice($shippingCode);

                    // If the carrier has a price
                    if ($carrierPrice) {
                        $code = $shippingCode . '_' . $methodCode;
                        $carrierTitle = $this->getCarrierTitle($shippingCode);
                        $methods[] = [
                            'carrier_code' => $code,
                            'label'=> $carrierTitle,
                            'price' => $carrierPrice,
                            'method_code' => $methodCode
                        ];
                    }
                }
            }
       }

       return $methods;
    }

    /**
     * Get the carrier price.
     */
    public function getCarrierPrice($shippingCode) {
        return $this->configHelper->value(
            'carriers/'. $shippingCode . '/price',
            true
        );
    }

    /**
     * Get the carrier title.
     */
    public function getCarrierTitle($shippingCode) {
        return $this->configHelper->value(
            'carriers/'. $shippingCode . '/title',
            true
        );
    }

    /**
     * Checks if any shipping method available.
     *
     * @param Address $address
     * @return bool
     */
    private function areShippingMethodsAvailable(Address $address): bool
    {
        $carriersForAddress = $this->carrierFinder->getCarriersForCustomerAddress($address);
        return !empty($carriersForAddress);
    }
}
