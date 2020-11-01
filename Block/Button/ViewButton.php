<?php
namespace Naxero\BuyNow\Block\Button;

/**
 * ViewButton class constructor.
 */
class ViewButton extends \Magento\Framework\View\Element\Template
{
    const MODE = 'page';

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
        \Naxero\BuyNow\Helper\Block $blockHelper,
        \Naxero\BuyNow\Helper\Config $configHelper,
        \Naxero\BuyNow\Helper\Purchase $purchaseHelper,
        \Naxero\BuyNow\Helper\Product $productHelper,
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
            $this->getProduct()->getId()
        );

        // Set the display mode
        $config['product']['display'] = self::MODE;

        // Check the display conditions
        $condition = $config['buttons']['show_guest_button']
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
