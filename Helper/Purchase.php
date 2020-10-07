<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

/**
 * Class Product
 */
class Purchase extends \Magento\Framework\App\Helper\AbstractHelper
{
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
     * @var ShippingSelector
     */
    private $shippingSelector;

    /**
     * @var Config
     */
    public $configHelper;

    /**
     * @var Product
     */
    public $productHelper;

    /**
     * @var Customer
     */
    public $customerHelper;

    /**
     * @var VaultHandlerService
     */
    public $vaultHandler;

    /**
     * Class Customer constructor.
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\InstantPurchase\Model\Ui\CustomerAddressesFormatter $customerAddressesFormatter,
        \Magento\InstantPurchase\Model\Ui\ShippingMethodFormatter $shippingMethodFormatter,
        \Naxero\AdvancedInstantPurchase\Model\InstantPurchase\ShippingSelector $shippingSelector,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Product $productHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper,
        \Naxero\AdvancedInstantPurchase\Model\Service\VaultHandlerService $vaultHandler
    ) {
        $this->customerSession = $customerSession;
        $this->customerAddressesFormatter = $customerAddressesFormatter;
        $this->shippingMethodFormatter = $shippingMethodFormatter;
        $this->shippingSelector = $shippingSelector;
        $this->productHelper = $productHelper;
        $this->configHelper = $configHelper;
        $this->customerHelper = $customerHelper;
        $this->vaultHandler = $vaultHandler;
    }

    /**
     * Get the popup data.
     */
    public function getPopupData()
    {
        return [
            'title' => $this->configHelper->value('display/popup_title'),
            'header_text' => $this->configHelper->value('display/popup_header_text'),
            'footer_text' => $this->configHelper->value('display/popup_footer_text')
        ];
    }

    /**
     * Get the customer purchase data.
     */
    public function getPurchaseData()
    {
        // Set the instant purchase availability
        $data = ['available' => true];

        // Load the customer
        $customer = $this->customerSession->getCustomer();
        
        // Customer data
        $paymentToken = $this->vaultHandler->preparePaymentToken();
        $shippingAddress = $customer->getDefaultShippingAddress();
        $billingAddress = $customer->getDefaultBillingAddress();
        $shippingMethod = $this->shippingSelector->getShippingRates($customer)[0];
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
                'carrier' => $shippingMethod['carrier'],
                'method' => $shippingMethod['method'],
                'summary' => $this->shippingMethodFormatter->format($shippingMethod),
            ]
        ];

        return $data;
    }

    /**
     * Get the confirmation modal content.
     */
    public function getConfirmContent()
    {
        // Prepare the output array
        $confirmationData = [];
        $confirmationData['popup'] = $this->getPopupData();
        $confirmationData['product'] = $this->productHelper->getData();
        $confirmationData['addresses'] = [];
        $confirmationData['savedCards'] = [];
        $confirmationData['shippingRates'] = [];

        // Build the confirmation data
        if ($this->customerHelper->isLoggedIn()) {
            // Load the customer
            $customer = $this->customerSession->getCustomer();

            // Confirmation data
            $confirmationData['addresses'] = $this->customerSession->getCustomer()->getAddresses();
            $confirmationData['savedCards'] = $this->vaultHandler->getUserCards();
            $confirmationData['shippingRates'] = $this->shippingSelector->getShippingRates(
                $customer
            );

            // Instant purchase data
            $purchaseData = $this->getPurchaseData();
            if (!empty($purchaseData)) {
                $confirmationData['sectionData'] = $purchaseData;
            }
        
        }

        return $confirmationData;
    }

    /**
     * Check if the purchase button can be displayed.
     */
    public function canDisplayButton()
    {
        return $this->bypassLogin();
    }

    /**
     * Check if the purchase button can bypass login.
     */
    public function bypassLogin()
    {
        return $this->configHelper->value('general/enabled')
        && $this->configHelper->value('guest/show_guest_button');
    }

    /**
     * Check if the purchase button should be disabled.
     */
    public function getButtonState()
    {
        return $this->configHelper->value('guest/click_event') == 'disabled'
        ? 'disabled="disabled' : '';
    }

    /**
     * Get the logged in button classes.
     */
    public function getButtonCss()
    {
        return $this->customerHelper->isLoggedIn()
        ? 'aip-login-popup' : '';
    }
}