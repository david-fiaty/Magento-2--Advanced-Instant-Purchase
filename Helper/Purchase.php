<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

/**
 * Class Product
 */
class Purchase extends \Magento\Framework\App\Helper\AbstractHelper
{
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
     * Class Customer constructor.
     */
    public function __construct(
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Product $productHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper
    ) {
        $this->productHelper = $productHelper;
        $this->configHelper = $configHelper;
        $this->customerHelper = $customerHelper;
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
        return [
            'advancedInstantPurchase' => array_merge(
                $this->configHelper->getValues(),
                []
            )
        ];
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
            // Load the customer data
            $this->customerHelper->init();

            // Confirmation data
            $confirmationData['addresses'] = $this->customerHelper->getAddresses();
            $confirmationData['savedCards'] = $this->customerData->vaultHandler->getUserCards();
            $confirmationData['shippingRates'] = $this->customerData->shippingSelector->getShippingRates(
                $this->customer
            );

            // Instant purchase data
            $customerSectionData = $this->customerData->getSectionData(
                $this->customerHelper->customer
            );
            
            if (!empty($customerSectionData)) {
                $confirmationData['sectionData'] = $customerSectionData;
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
        return $this->configHelper->value('guest/click_event') != 'disabled'
        ? '' : 'disabled';
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