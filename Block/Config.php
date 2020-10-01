<?php
namespace Naxero\AdvancedInstantPurchase\Block;

/**
 * Configuration for JavaScript instant purchase button component.
 */
class Config extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Config
     */
    public $configHelper;

    /**
     * Button class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configHelper = $configHelper;
    }

    /**
     * Get the module config values.
     */
    public function getConfig()
    {
        $aipConfig = $this->configHelper->getFilteredValues();

        return json_encode($aipConfig);
    }
}
