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
        \Naxero\BuyNow\Model\Order\ShippingSelector $shippingSelector,
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
        
        // Data
        $data += [
            'payment_token' => $this->vaultHandler->preparePaymentToken(),
            'shipping_address' => $this->buildShippingAddressArray(),
            'billing_address' => $this->buildBillingAddressArray(),
            'shipping_method' => $this->buildShippingMethodArray()
        ];

        return $data;
    }

    /**
     * Build the shipping address array.
     */
    public function buildShippingAddressArray() {
        $shippingAddress = $this->customerHelper->getShippingAddress();

        return [
            'id' => !$shippingAddress ? 0 : $shippingAddress->getId(),
            'summary' => !$shippingAddress ? '' : $this->customerAddressesFormatter->format($shippingAddress)
        ];
    }

    /**
     * Build the billing address array.
     */
    public function buildBillingAddressArray() {
        $billingAddress = $this->customerHelper->getBillingAddress();

        return [
            'id' => !$billingAddress ? 0 : $billingAddress->getId(),
            'summary' => !$billingAddress ? '' : $this->customerAddressesFormatter->format($billingAddress)
        ];
    }

    /**
     * Build the shipping method array.
     */
    public function buildShippingMethodArray() {
        $shippingMethod = $this->shippingSelector->getShippingMethod($this->customerHelper->getCustomer());

        return [
            'carrier' => !$shippingMethod ? '' : $shippingMethod->getCarrierCode(),
            'method' => !$shippingMethod ? '' : $shippingMethod->getMethodCode(),
            'summary' => !$shippingMethod ? '' : $this->shippingMethodFormatter->format($shippingMethod)
        ];
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
        $condition = $this->configHelper->value('buttons/show_guest_button')
        && $this->configHelper->value('general/enabled');

        return $this->bypassLogin() || $condition;
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
     * Get the button CSS classes.
     */
    public function getButtonCss()
    {
        // Load the default config classes
        $classes = $this->configHelper->value('buttons/button_classes');

        // Add additional classes
        $classes .= $this->customerHelper->isLoggedIn()
        ? ' nbn-login-popup' : '';

        return $classes;
    }
}
