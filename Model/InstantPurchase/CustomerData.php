<?php
namespace Naxero\AdvancedInstantPurchase\Model\InstantPurchase;

/**
 * Class CustomerData
 */
class CustomerData implements \Magento\Customer\CustomerData\SectionSourceInterface
{
    /**
     * @var Config
     */
    public $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @var CustomerAddressesFormatter
     */
    public $customerAddressesFormatter;

    /**
     * @var ShippingMethodFormatter
     */
    public $shippingMethodFormatter;

    /**
     * @var ShippingMethodInterface
     */
    public $shippingMethodInterface;

    /**
     * @var VaultHandlerService
     */
    public $vaultHandler;

    /**
     * @var AvailabilityChecker
     */
    public $availabilityChecker;

    /**
     * InstantPurchase constructor.
     */
    public function __construct(
        \Naxero\AdvancedInstantPurchase\Helper\Config $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\InstantPurchase\Model\Ui\CustomerAddressesFormatter $customerAddressesFormatter,
        \Magento\Quote\Api\Data\ShippingMethodInterface $shippingMethodInterface,
        \Magento\InstantPurchase\Model\Ui\ShippingMethodFormatter $shippingMethodFormatter,
        \Naxero\AdvancedInstantPurchase\Model\Service\VaultHandlerService $vaultHandler,
        \Naxero\AdvancedInstantPurchase\Model\InstantPurchase\AvailabilityChecker $availabilityChecker
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->customerAddressesFormatter = $customerAddressesFormatter;
        $this->shippingMethodInterface = $shippingMethodInterface;
        $this->shippingMethodFormatter = $shippingMethodFormatter;
        $this->vaultHandler = $vaultHandler;
        $this->availabilityChecker = $availabilityChecker;
    }

    /**
     * @inheritdoc
     */
    public function getSectionData() : array
    {
        // Set the instant purchase availability
        $isAvailalbe = $this->availabilityChecker->isAvailable();
        $data = ['available' => $isAvailalbe];
        if (!$isAvailalbe) {
            return $data;
        }

        // Customer data
        $paymentToken = $this->vaultHandler->preparePaymentToken();
        $shippingAddress = $this->getShippingAddress();
        $billingAddress = $this->getBillingAddress();
        $shippingMethod = $this->shippingMethodInterface;
        $data += [
            'paymentToken' => $paymentToken,
            'shippingAddress' => [
                'id' => $shippingAddress->getId(),
                'summary' => $this->customerAddressesFormatter->format($shippingAddress),
            ],
            'billingAddress' => [
                'id' => $billingAddress->getId(),
                'summary' => $this->customerAddressesFormatter->format($billingAddress),
            ],
            'shippingMethod' => [
                'carrier' => $shippingMethod->getCarrierCode(),
                'method' => $shippingMethod->getMethodCode(),
                'summary' => $this->shippingMethodFormatter->format($shippingMethod),
            ]
        ];

        return ['customer_data' => $data];
    }

    /**
     * Get the defult user shipping address.
     */
    public function getShippingAddress()
    {
        return $this->customerSession->getCustomer()->getDefaultShippingAddress();
    }

    /**
     * Get the defult user billing address.
     */
    public function getBillingAddress()
    {
        return $this->customerSession->getCustomer()->getDefaultBillingAddress();
    }
}
