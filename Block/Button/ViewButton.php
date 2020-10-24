<?php
namespace Naxero\AdvancedInstantPurchase\Block\Button;

/**
 * ViewButton class constructor.
 */
class ViewButton extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Registry
     */
    public $registry; 

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
        \Magento\Framework\Registry $registry,
        \Naxero\AdvancedInstantPurchase\Helper\Block $blockHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Purchase $purchaseHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Product $productHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        
        $this->registry = $registry;
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
            $this->getProduct()->getId(),
            'view'
        );

        // Set the display mode
        $config['product']['display'] = 'view';

        // Check the display conditions
        $condition = $config['guest']['show_guest_button']
        && $config['general']['enabled']
        && $config['products']['product_view']
        && $this->purchaseHelper->canDisplayButton();

        return $condition ? $config : null;
    }
    
    /**
     * Get the current product.
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }
}