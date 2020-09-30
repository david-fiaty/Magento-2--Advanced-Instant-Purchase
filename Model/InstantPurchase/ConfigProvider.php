<?php
namespace Naxero\AdvancedInstantPurchase\Model\InstantPurchase;

/**
 * Class ConfigProvider
 */
class ConfigProvider implements \Magento\Customer\CustomerData\SectionSourceInterface
{
    /**
     * @var Config
     */
    public $config;

    /**
     * @var Purchase
     */
    public $purchaseHelper;

    /**
     * InstantPurchase constructor.
     */
    public function __construct(
        \Naxero\AdvancedInstantPurchase\Helper\Config $config,
        \Naxero\AdvancedInstantPurchase\Helper\Purchase $purchaseHelper
    ) {
        $this->config = $config;
        $this->purchaseHelper = $purchaseHelper;
    }

    /**
     * @inheritdoc
     */
    public function getSectionData() : array
    {
        return $this->purchaseHelper->getData();
    }
}
