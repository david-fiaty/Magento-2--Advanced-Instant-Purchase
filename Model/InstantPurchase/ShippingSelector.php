<?php
namespace Naxero\AdvancedInstantPurchase\Model\InstantPurchase;

use Magento\Framework\DataObject;

/**
 * Class ShippingSelector.
 */
class ShippingSelector
{    
    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

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
    public $carriersConfig;

    /**
     * @var AddressFactory
     */
    public $addressFactory;

    /**
     * @var Config
     */
    public $configHelper;

    /**
     * ShippingSelector constructor.
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Quote\Api\Data\ShippingMethodInterfaceFactory $shippingMethodFactory,
        \Magento\Shipping\Model\Config $shippingModel,
        \Magento\Shipping\Model\Config $carriersConfig,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper
    ) {
        $this->storeManager = $storeManager;
        $this->shippingMethodFactory = $shippingMethodFactory;
        $this->shippingModel = $shippingModel;
        $this->carriersConfig = $carriersConfig;
        $this->addressFactory = $addressFactory;
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
        $rates = $this->getShippingRates($customer)[0];
        $shippingMethod = $this->shippingMethodFactory->create()
            ->setCarrierCode($rates['carrier_code'])
            ->setMethodCode($rates['method_code'])
            ->setMethodTitle(__('My method xxx'))
            ->setAvailable(
                $this->areShippingMethodsAvailable(
                    $this->getShippingAddress($customer)
                )
            );

        return $shippingMethod;
    }

    /**
     * Get a shipping address.
     */
    public function getShippingAddress($customer)
    {
        $shippingAddressId = $customer->getDefaultShipping();
        return $this->addressFactory->create()->load($shippingAddressId);
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
    public function areShippingMethodsAvailable(Address $address): bool
    {
        $carriersForAddress = $this->carrierFinder->getCarriersForCustomerAddress($address);
        return !empty($carriersForAddress);
    }

    /**
     * Finds carriers delivering to customer address
     *
     * @param Address $address
     * @return array
     */
    public function getCarriersForCustomerAddress(Address $address): array
    {
        $request = new DataObject([
            'dest_country_id' => $address->getCountryId()
        ]);

        $carriers = [];
        foreach ($this->carriersConfig->getActiveCarriers($this->storeManager->getStore()->getId()) as $carrier) {
            $checked = $carrier->checkAvailableShipCountries($request);
            if (false !== $checked && null === $checked->getErrorMessage() && !empty($checked->getAllowedMethods())) {
                $carriers[] = $checked;
            }
        }

        return $carriers;
    }
}
