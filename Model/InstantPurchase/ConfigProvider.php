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
     * InstantPurchase constructor.
     */
    public function __construct(
        \Naxero\AdvancedInstantPurchase\Helper\Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function getSectionData() : array
    {
        return ['advancedInstantPurchase' => $this->config->getValues()];
    }
}
