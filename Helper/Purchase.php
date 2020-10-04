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
     * @var CustomerData
     */
    public $customerData;

    /**
     * Class Customer constructor.
     */
    public function __construct(
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Product $productHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper,
        \Naxero\AdvancedInstantPurchase\Model\InstantPurchase\CustomerData $customerData
    ) {
        $this->customerData = $customerData;
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
        $aipConfig = $this->configHelper->getValues();  
        return [
            'advancedInstantPurchase' => array_merge(
                $aipConfig, 
                $this->customerData->getSectionData()
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
            $this->customerHelper->loadCustomerData();

            // Confirmation data
            $confirmationData['addresses'] = $this->customerHelper->getAddresses();
            $confirmationData['savedCards'] = $this->customerData->vaultHandler->getUserCards();
            $confirmationData['shippingRates'] = $this->customerData->shippingSelector->getShippingRates(
                $this->customer
            );

            // Instant purchase data
            $customerSectionData = $this->customerData->getSectionData($this->customer);
            if (!empty($customerSectionData)) {
                $confirmationData['sectionData'] = $customerSectionData;
            }
        }

        return $confirmationData;
    }
}