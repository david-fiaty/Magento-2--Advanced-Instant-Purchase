<?php
namespace Naxero\BuyNow\Helper;

use Naxero\BuyNow\Model\Config\Naming;

/**
 * Class Purchase Helper.
 */
class Purchase extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var RequestInterface
     */
    public $request;

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
     * @var Block
     */
    public $blockHelper;

    /**
     * @var Product
     */
    public $productHelper;

    /**
     * @var Payment
     */
    public $paymentHelper;

    /**
     * @var Customer
     */
    public $customerHelper;

    /**
     * @var VaultHandlerService
     */
    public $vaultHandler;

    /**
     * Class Purchase helper constructor.
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\InstantPurchase\Model\Ui\CustomerAddressesFormatter $customerAddressesFormatter,
        \Magento\InstantPurchase\Model\Ui\ShippingMethodFormatter $shippingMethodFormatter,
        \Naxero\BuyNow\Model\InstantPurchase\ShippingSelector $shippingSelector,
        \Naxero\BuyNow\Helper\Config $configHelper,
        \Naxero\BuyNow\Helper\Product $productHelper,
        \Naxero\BuyNow\Helper\Block $blockHelper,
        \Naxero\BuyNow\Helper\Payment $paymentHelper,
        \Naxero\BuyNow\Helper\Customer $customerHelper,
        \Naxero\BuyNow\Model\Service\VaultHandlerService $vaultHandler
    ) {
        $this->request = $request;
        $this->customerAddressesFormatter = $customerAddressesFormatter;
        $this->shippingMethodFormatter = $shippingMethodFormatter;
        $this->shippingSelector = $shippingSelector;
        $this->productHelper = $productHelper;
        $this->paymentHelper = $paymentHelper;
        $this->configHelper = $configHelper;
        $this->blockHelper = $blockHelper;
        $this->customerHelper = $customerHelper;
        $this->vaultHandler = $vaultHandler;
    }

    /**
     * Get the popup data.
     */
    public function getPopupSettings()
    {
        return [
            'title' => $this->configHelper->value('popups/popup_title'),
            'header_text' => $this->configHelper->value('popups/popup_header_text'),
            'footer_text' => $this->configHelper->value('popups/popup_footer_text')
        ];
    }

    /**
     * Get the customer purchase data.
     */
    public function getPurchaseData()
    {
        // Set the instant purchase availability
        $data = ['available' => true];
        
        // Payment token
        $paymentToken = $this->vaultHandler->preparePaymentToken();

        // Shipping address
        $shippingAddress = $this->customerHelper->getShippingAddress();

        // Billing address
        $billingAddress = $this->customerHelper->getBillingAddress();

        // Shipping method
        $shippingMethod = $this->shippingSelector->getShippingMethod($this->customerHelper->getCustomer());

        // Data
        $data += [
            'payment_token' => $paymentToken,
            'shipping_address' => [
                'id' => $shippingAddress->getId(),
                'summary' => $this->customerAddressesFormatter->format($shippingAddress),
            ],
            'billing_address' => [
                'id' => $billingAddress->getId(),
                'summary' => $this->customerAddressesFormatter->format($billingAddress),
            ],
            'shipping_method' => [
                'carrier' => $shippingMethod->getCarrierCode(),
                'method' => $shippingMethod->getMethodCode(),
                'summary' => $this->shippingMethodFormatter->format($shippingMethod),
            ]
        ];

        return $data;
    }

    /**
     * Get the confirmation modal content.
     */
    public function getConfirmContent($productId = 0)
    {
        // Get the product id
        $productId = $productId > 0 ? $productId : $this->request->getParam('product_id');

        // Prepare the output array
        $confirmationData = [
            'popup' => $this->getPopupSettings(),
            'product' => $this->productHelper->getData($productId),
            'addresses' => [],
            'savedCards' => [],
            'shippingRates' => [],
            'config' => $this->blockHelper->getConfig($productId)
        ];

        // Build the confirmation data
        if ($this->customerHelper->isLoggedIn()) {
            // Load the customer
            $customer = $this->customerHelper->getCustomer();

            // Confirmation data
            $confirmationData['addresses'] = $customer->getAddresses();
            $confirmationData['savedCards'] = $this->vaultHandler->getAllowedCards();
            $confirmationData['otherPaymentMethods'] = $this->paymentHelper->getOtherPaymentMethods();
            $confirmationData['shippingRates'] = $this->shippingSelector->getShippingRates($customer);

            // Instant purchase data
            $purchaseData = $this->getPurchaseData();
            if (!empty($purchaseData)) {
                $confirmationData['purchase_data'] = $purchaseData;
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
        && $this->configHelper->value('buttons/show_guest_button');
    }

    /**
     * Get the logged in button classes.
     */
    public function getButtonCss()
    {
        return $this->customerHelper->isLoggedIn()
        ? 'nbn-login-popup' : '';
    }
}
