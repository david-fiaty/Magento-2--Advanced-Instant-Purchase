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
     * @var InstantPurchaseInterface
     */
    private $instantPurchase;

    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @var CustomerAddressesFormatter
     */
    private $customerAddressesFormatter;

    /**
     * @var ShippingMethodFormatter
     */
    private $shippingMethodFormatter;

    /**
     * @var VaultHandlerService
     */
    private $vaultHandler;

    /**
     * @var AvailabilityChecker
     */
    private $availabilityChecker;

    /**
     * @var PaymentTokenFormatter
     */
    private $paymentTokenFormatter;

    /**
     * InstantPurchase constructor.
     */
    public function __construct(
        \Naxero\AdvancedInstantPurchase\Helper\Config $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\InstantPurchase\Model\InstantPurchaseInterface $instantPurchase,
        \Magento\Customer\Model\Session $customerSession,
        \Naxero\AdvancedInstantPurchase\Model\InstantPurchase\TokenFormatter $paymentTokenFormatter,
        \Magento\InstantPurchase\Model\Ui\CustomerAddressesFormatter $customerAddressesFormatter,
        \Magento\InstantPurchase\Model\Ui\ShippingMethodFormatter $shippingMethodFormatter,
        \Naxero\AdvancedInstantPurchase\Model\Service\VaultHandlerService $vaultHandler,
        \Naxero\AdvancedInstantPurchase\Model\InstantPurchase\AvailabilityChecker $availabilityChecker
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->instantPurchase = $instantPurchase;
        $this->customerSession = $customerSession;
        $this->customerAddressesFormatter = $customerAddressesFormatter;
        $this->shippingMethodFormatter = $shippingMethodFormatter;
        $this->vaultHandler = $vaultHandler;
        $this->availabilityChecker = $availabilityChecker;
        $this->paymentTokenFormatter = $paymentTokenFormatter;
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

        // Get the card data
        $paymentToken = $this->vaultHandler->getLastSavedCard();

        // Load the option
        $instantPurchaseOption = $this->instantPurchase->getOption(
            $this->storeManager->getStore(),
            $this->customerSession->getCustomer()
        );

        // Get the required data
        if ($instantPurchaseOption) {
            $shippingAddress = $instantPurchaseOption->getShippingAddress();
            $billingAddress = $instantPurchaseOption->getBillingAddress();
            $shippingMethod = $instantPurchaseOption->getShippingMethod();
            $data += [
                'aiiConfig' => $this->config->getValues(),
                'paymentToken' => [
                    'publicHash' => $paymentToken->getPublicHash(),
                    'summary' => $this->paymentTokenFormatter->formatPaymentToken($paymentToken),
                ],
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
        }

        return $data;
    }
}
