<?php
namespace Naxero\AdvancedInstantPurchase\Model\InstantPurchase\Loaders;

use Magento\Customer\Model\Address;
use Magento\InstantPurchase\Model\ShippingMethodChoose\DeferredShippingMethodChooserInterface;
use Magento\InstantPurchase\Model\ShippingMethodChoose\CheapestMethodDeferredChooser;

/**
 * Creates special shipping method to choose cheapest shipping method after quote creation.
 */
class CheapestMethodChooser implements \Magento\InstantPurchase\Model\ShippingMethodChoose\ShippingMethodChooserInterface
{
    /**
     * @var ShippingMethodInterfaceFactory
     */
    private $shippingMethodFactory;

    /**
     * @var CarrierFinder
     */
    private $carrierFinder;

    /**
     * CheapestMethodChooser constructor.
     * @param ShippingMethodInterfaceFactory $shippingMethodFactory
     * @param CarrierFinder $carrierFinder
     */
    public function __construct(
        \Magento\Quote\Api\Data\ShippingMethodInterfaceFactory $shippingMethodFactory,
        \Magento\InstantPurchase\Model\ShippingMethodChoose\CarrierFinder $carrierFinder
    ) {
        $this->shippingMethodFactory = $shippingMethodFactory;
        $this->carrierFinder = $carrierFinder;
    }

    /**
     * @inheritdoc
     */
    public function choose(Address $address)
    {
        $shippingMethod = $this->shippingMethodFactory->create()
            ->setCarrierCode(DeferredShippingMethodChooserInterface::CARRIER)
            ->setMethodCode(CheapestMethodDeferredChooser::METHOD_CODE)
            ->setMethodTitle(__('Cheapest price'))
            ->setAvailable($this->areShippingMethodsAvailable($address));
        return $shippingMethod;
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
