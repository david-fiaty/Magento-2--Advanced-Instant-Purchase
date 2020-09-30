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
     * @var Product
     */
    public $productHelper;

    /**
     * Button class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Product $productHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configHelper = $configHelper;
        $this->productHelper = $productHelper;
    }

    /**
     * Get the module config values.
     */
    public function getConfig()
    {
        // Get the module config
        $aipConfig = $this->configHelper->getFilteredValues();

        // Product info
        $aipConfig['product'] = $this->productHelper->getData();
        $aipConfig['isListView'] = $this->productHelper->isListView();

        return json_encode($aipConfig);
    }
}
