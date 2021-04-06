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
    public $shippingSelector;

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
            'payment_token' => $this->buildPaymentTokenArray(),
            'shipping_address' => $this->buildShippingAddressArray(),
            'billing_address' => $this->buildBillingAddressArray(),
            'shipping_method' => $this->buildShippingMethodArray()
        ];

        return $data;
    }

    /**
     * Build the payment token array.
     */
    public function buildPaymentTokenArray($paymentToken = null)
    {
        // Prepare the output array
        $paymentTokenData = [
            'public_hash' => '',
            'summary' => '',
            'method_code' => ''
        ];

        // Get the customer data
        $customerData = $this->blockHelper->getCustomerData();

        // Get the payment token data
        if ($this->customerDataValid($customerData)) {
            $paymentTokenData = $this->vaultHandler->preparePaymentToken($customerData['entity_id']);
        }

        return $paymentTokenData;
    }

    /**
     * Check if the customer data is valid.
     */
    public function customerDataValid($customerData)
    {
        return $customerData && !empty($customerData)
        && isset($customerData['entity_id']) 
        && (int) $customerData['entity_id'] > 0;
    }

    /**
     * Build the shipping address array.
     */
    public function buildShippingAddressArray($shippingAddress = null)
    {
        // Get the customer data
        $customerData = $this->blockHelper->getCustomerData();

        // Get the shipping address data
        if ($this->customerDataValid($customerData)) {
            $shippingAddress = $this->customerHelper->getShippingAddress($customerData['entity_id']);
        }

        // Return the shipping address array
        return [
            'id' => !$shippingAddress ? 0 : $shippingAddress->getId(),
            'summary' => !$shippingAddress ? '' : $this->customerAddressesFormatter->format($shippingAddress)
        ];
    }

    /**
     * Build the billing address array.
     */
    public function buildBillingAddressArray($billingAddress = null)
    {
        // Get the customer data
        $customerData = $this->blockHelper->getCustomerData();

        // Get the billing address data
        if ($this->customerDataValid($customerData)) {
            $billingAddress = $this->customerHelper->getBillingAddress($customerData['entity_id']);
        }
        
        return [
            'id' => !$billingAddress ? 0 : $billingAddress->getId(),
            'summary' => !$billingAddress ? '' : $this->customerAddressesFormatter->format($billingAddress)
        ];
    }

    /**
     * Build the shipping method array.
     */
    public function buildShippingMethodArray($shippingMethod = null)
    {
        // Get the customer data
        $customerData = $this->blockHelper->getCustomerData();

        // Get the shipping method data
        if ($this->customerDataValid($customerData)) {
            $shippingMethod = $this->shippingSelector->getShippingMethod(
                $this->customerHelper->getCustomer($customerData['entity_id'])
            );
        }

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
            // Get the customer data
            $customerData = $this->blockHelper->getCustomerData();

            // Load the customer
            $customer = $this->customerHelper->getCustomer($customerData['entity_id']);

            // Confirmation data
            $confirmationData['addresses'] = $this->customerHelper->getAddresses();
            $confirmationData['savedCards'] = $this->vaultHandler->getAllowedCards();
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
    public function canDisplayButton($config)
    {
        // Button available
        $buttonEnabled = $config['general']['enabled'];
        $isLoggedIn = $this->customerHelper->isLoggedIn();
        $showGuestButton = !$isLoggedIn && $config['buttons']['show_guest_button'];
        $isGroupValid = $this->customerHelper->canDisplayForGroup($config);
        
        return $buttonEnabled && $isGroupValid && $isTimeValid
        && ($isLoggedIn || $showGuestButton);
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
