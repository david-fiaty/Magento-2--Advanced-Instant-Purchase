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

namespace Naxero\BuyNow\Model\Order;

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
     * @var Config
     */
    public $configHelper;

    /**
     * @var Customer
     */
    public $customerHelper;

    /**
     * ShippingSelector constructor.
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Quote\Api\Data\ShippingMethodInterfaceFactory $shippingMethodFactory,
        \Magento\Shipping\Model\Config $shippingModel,
        \Magento\Shipping\Model\Config $carriersConfig,
        \Naxero\BuyNow\Helper\Config $configHelper,
        \Naxero\BuyNow\Helper\Customer $customerHelper
    ) {
        $this->storeManager = $storeManager;
        $this->shippingMethodFactory = $shippingMethodFactory;
        $this->shippingModel = $shippingModel;
        $this->carriersConfig = $carriersConfig;
        $this->configHelper = $configHelper;
        $this->customerHelper = $customerHelper;
    }

    /**
     * Selects a shipping method.
     *
     * @param  Address $address
     * @return Rate
     */
    public function getShippingMethod($customer)
    {
        // Get the shipping rates
        $rates = $this->getShippingRates($customer);

        // Build the shipping method
        if ($rates && is_array($rates) && isset($rates[0]) && !empty($rates[0])) {
            // Get the carrier
            if (isset($rates['carrier_code'])) {
                $shippingMethod = $this->shippingMethodFactory->create();
                $shippingMethod->setCarrierCode($rates['carrier_code']);
                $shippingMethod->setMethodTitle($rates['carrier_title']);
                $shippingMethod->setMethodCode($rates['method_code']);
                $shippingMethod->setAvailable(
                    $this->areShippingMethodsAvailable(
                        $this->customerHelper->getShippingAddress()
                    )
                );

                return $shippingMethod;
            }
        }

        return null;
    }

    /**
     * Load a shipping method.
     */
    public function loadShippingMethod($address, $code)
    {
        $address->setCollectShippingRates(true);
        $address->collectShippingRates();
        $shippingRates = $address->getAllShippingRates();

        foreach ($shippingRates as $shippingRate) {
            $rate = $shippingRate->getData();
            if ($rate['code'] == $code) {
                $shippingMethod = $this->shippingMethodFactory->create();
                $shippingMethod->setCarrierCode($rate['carrier_code']);
                $shippingMethod->setMethodTitle($rate['carrier_title']);
                $shippingMethod->setMethodCode($rate['method_code']);
                $shippingMethod->setAvailable(
                    $this->areShippingMethodsAvailable(
                        $this->customerHelper->getShippingAddress()
                    )
                );

                return $shippingMethod;
            }
        }

        return null;
    }

    /**
     * Gets all shipping methods avaiable.
     *
     * @param  Customer $customer
     * @return Array
     */
    public function getShippingRates($customer)
    {
        $carriers = $this->shippingModel->getActiveCarriers();
        $methods = [];
        foreach ($carriers as $shippingCode => $carrier) {
            $carrierMethods = $carrier->getAllowedMethods();
            if ($carrierMethods) {
                foreach ($carrierMethods as $methodCode => $method) {
                    $isTableRate = $shippingCode == 'tablerate';
                    if (!$isTableRate) {
                        $carrierPrice = $this->getCarrierPrice($shippingCode);
                        $carrierTitle = $this->getCarrierTitle($shippingCode);
                        $methods[] = [
                            'carrier_code' => $carrier->getCarrierCode(),
                            'carrier_title' => $carrierTitle,
                            'carrier_price' => $carrierPrice ? $carrierPrice : 0,
                            'method_code' => $methodCode
                        ];
                    }
                }
            }
        }

        return $methods;
    }

    /**
     * Get the carrier data by code.
     */
    public function getCarrierData($carrierCode, $customer)
    {
        $methods = $this->getShippingRates($customer);
        foreach ($methods as $method) {
            if ($method['carrier_code'] == $carrierCode) {
                return $method;
            }
        }

        return null;
    }

    /**
     * Get the carrier price.
     */
    // Todo - Use getCarrierData method to get the price
    public function getCarrierPrice($shippingCode)
    {
        return $this->configHelper->value(
            'carriers/' . $shippingCode . '/price',
            true
        );
    }

    /**
     * Get the carrier title.
     */
    public function getCarrierTitle($shippingCode)
    {
        return $this->configHelper->value(
            'carriers/' . $shippingCode . '/title',
            true
        );
    }

    /**
     * Checks if any shipping method available.
     *
     * @param  Address $address
     * @return bool
     */
    public function areShippingMethodsAvailable($address)
    {
        $carriersForAddress = $this->getCarriersForCustomerAddress($address);
        return !empty($carriersForAddress);
    }

    /**
     * Finds carriers delivering to customer address
     *
     * @param  Address $address
     * @return array
     */
    public function getCarriersForCustomerAddress($address)
    {
        $request = new DataObject(
            [
            'dest_country_id' => $address->getCountryId()
            ]
        );

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
