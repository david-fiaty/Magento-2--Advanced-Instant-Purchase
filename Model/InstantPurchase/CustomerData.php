<?php
namespace Naxero\AdvancedInstantPurchase\Model\InstantPurchase;

/**
 * Class CustomerData
 */
class CustomerData
{
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
     * @var Customer
     */
    public $customerHelper;

    /**
     * @var ShippingSelector
     */
    public $shippingSelector;

    /**
     * InstantPurchase constructor.
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\InstantPurchase\Model\Ui\CustomerAddressesFormatter $customerAddressesFormatter,
        \Magento\Quote\Api\Data\ShippingMethodInterface $shippingMethodInterface,
        \Magento\InstantPurchase\Model\Ui\ShippingMethodFormatter $shippingMethodFormatter,
        \Naxero\AdvancedInstantPurchase\Model\Service\VaultHandlerService $vaultHandler,
        \Naxero\AdvancedInstantPurchase\Model\InstantPurchase\AvailabilityChecker $availabilityChecker,
        \Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper,
        \Naxero\AdvancedInstantPurchase\Model\InstantPurchase\ShippingSelector $shippingSelector
    ) {
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->customerAddressesFormatter = $customerAddressesFormatter;
        $this->shippingMethodInterface = $shippingMethodInterface;
        $this->shippingMethodFormatter = $shippingMethodFormatter;
        $this->vaultHandler = $vaultHandler;
        $this->availabilityChecker = $availabilityChecker;
        $this->customerHelper = $customerHelper;
        $this->shippingSelector = $shippingSelector;
    }

    /**
     * @inheritdoc
     */
    public function getSectionData()
    {
        // Set the instant purchase availability
        $isAvailalbe = $this->availabilityChecker->isAvailable();
        $data = ['available' => $isAvailalbe];
        if (!$isAvailalbe) {
            return $data;
        }

        // Load the customer
        $customer = $this->customerHelper->loadCustomer();
        
        // Customer data
        $paymentToken = $this->vaultHandler->preparePaymentToken();
        $shippingAddress = $customer->getDefaultShippingAddress();
        $billingAddress = $customer->getDefaultBillingAddress();
        //$shippingMethod = $this->shippingMethodInterface;
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
                'carrier' => 'dd',
                'method' => 'ee',
                'summary' => 'ff'
            ]

            /*
            'shippingMethod' => [
                'carrier' => $shippingMethod->getCarrierCode(),
                'method' => $shippingMethod->getMethodCode(),
                'summary' => $this->shippingMethodFormatter->format($shippingMethod),
            ]
            */
        ];

        return ['customer_data' => $data];
    }
}
