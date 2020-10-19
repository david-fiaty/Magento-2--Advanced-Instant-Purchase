<?php
namespace Naxero\AdvancedInstantPurchase\Block\Button;

/**
 * BlockButton class constructor.
 */
class BlockButton extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Block
     */
    public $blockHelper;

    /**
     * @var Config
     */
    public $configHelper;

    /**
     * @var Purchase
     */
    public $purchaseHelper;

    /**
     * @var Product
     */
    public $productHelper;

    /**
     * ViewButton class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Naxero\AdvancedInstantPurchase\Helper\Block $blockHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Purchase $purchaseHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Product $productHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        
        $this->configHelper = $configHelper;
        $this->blockHelper = $blockHelper;
        $this->purchaseHelper = $purchaseHelper;
        $this->productHelper = $productHelper;
    }

    /**
     * Get the block config.
     */
    public function getConfig()
    {
        // Prepare the config
        $pid = $this->getData('product_id');
        $config = $this->blockHelper->getConfig($pid);

        // Check the display conditions
        $condition = $config['guest']['show_guest_button']
        && $config['general']['enabled']
        && $this->purchaseHelper->canDisplayButton();

        return $condition ? $config : null;
    }
    
    /**
     * Get the current product.
     */
    public function getProduct($pid = 0)
    {
        return $this->productHelper->getProduct($pid);
    }

    /**
     * Disable the block cache.
     */
    public function getCacheLifetime()
    {
        return null;
    }
}