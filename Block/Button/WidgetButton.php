<?php
namespace Naxero\AdvancedInstantPurchase\Block\Button;

/**
 * WidgetButton class constructor.
 */
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class WidgetButton extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    public $_template = "button/base.phtml";

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
     * BlockButton class constructor.
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
        $config = $this->blockHelper->getConfig(
            $this->getData('product_id')
        );

        // Set the display mode
        $config['product']['display'] = 'widget';

        // Check the display conditions
        $condition = $config['guest']['show_guest_button']
        && $config['general']['enabled']
        && $this->purchaseHelper->canDisplayButton();

        return $condition ? $config : null;
    }
    
    /**
     * Get the current product.
     */
    public function getProduct()
    {
        return $this->productHelper->getProduct(
            $this->getData('product_id')
        );
    }

    /**
     * Disable the block cache.
     */
    public function getCacheLifetime()
    {
        return null;
    }
}