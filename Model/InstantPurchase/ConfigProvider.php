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
     * @var Customer
     */
    public $customerHelper;

    /**
     * InstantPurchase constructor.
     */
    public function __construct(
        \Naxero\AdvancedInstantPurchase\Helper\Config $config,
        \Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper
    ) {
        $this->config = $config;
        $this->customerHelper = $customerHelper;
    }

    /**
     * @inheritdoc
     */
    public function getSectionData() : array
    {
        return $this->customerHelper->getPurchaseData();
    }
}
