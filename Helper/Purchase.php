<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

/**
 * Class Product
 */
class Purchase extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Image
     */
    public $imageHelper;

    /**
     * @var Data
     */
    public $priceHelper;

    /**
     * @var Registry
     */
    public $registry; 

    /**
     * @var RequestInterface
     */
    public $request; 

    /**
     * @var ProductFactory
     */
    public $productFactory; 

    /**
     * @var CustomerData
     */
    public $customerData;

    /**
     * Class Customer constructor.
     */
    public function __construct(
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Naxero\AdvancedInstantPurchase\Model\InstantPurchase\CustomerData $customerData
    ) {
        $this->registry = $registry;
        $this->imageHelper = $imageHelper;
        $this->priceHelper = $priceHelper;
        $this->request = $request;
        $this->productFactory = $productFactory;
        $this->customerData = $customerData;
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
        unset($aipConfig['card_form']);
        return [
            'advancedInstantPurchase' => array_merge(
                $aipConfig, 
                $this->customerData->getSectionData()
            )
        ];
    }
}
