<?php
namespace Naxero\AdvancedInstantPurchase\Block;

/**
 * Configuration for JavaScript instant purchase button component.
 */
class Config extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Repository
     */
    public $assetRepo; 

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
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Product $productHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->assetRepo = $assetRepo;
        $this->configHelper = $configHelper;
        $this->productHelper = $productHelper;
    }

    /**
     * Get the module config values.
     */
    public function getConfig()
    {
        // Get the module config
        $aipConfig = $this->configHelper->getValues();

        // Filter parameters
        unset($aipConfig['card_form']);

        // Loader icon
        $aipConfig['ui']['loader'] = $this->getLoaderIconUrl();

        // Product info
        $aipConfig['product'] = $this->productHelper->getData();
        $aipConfig['isListView'] = $this->productHelper->isListView();

        return json_encode($aipConfig);
    }

    /**
     * Get the loader icon URL.
     */
    public function getLoaderIconUrl()
    {
        return $this->assetRepo->getUrl('Naxero_AdvancedInstantPurchase::images/ajax-loader.gif');
    }
}
