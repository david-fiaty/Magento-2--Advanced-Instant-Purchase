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
     * @var CustomerData
     */
    public $customerData;

    /**
     * Class Customer constructor.
     */
    public function __construct(
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        \Naxero\AdvancedInstantPurchase\Model\InstantPurchase\CustomerData $customerData
    ) {
        $this->customerData = $customerData;
        $this->configHelper = $configHelper;
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
    public function getData()
    {
        $aipConfig = $this->configHelper->getValues();  
        return [
            'advancedInstantPurchase' => array_merge(
                $aipConfig, 
                $this->customerData->getSectionData()
            )
        ];
    }
}
