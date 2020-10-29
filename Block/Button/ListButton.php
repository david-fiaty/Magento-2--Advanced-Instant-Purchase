<?php
namespace Naxero\AdvancedInstantPurchase\Block\Button;

/**
 * ListButton class constructor.
 */
class ListButton extends \Magento\Catalog\Block\Product\ProductList\Item\Block
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
        \Magento\Catalog\Block\Product\Context $context,
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
            $this->getProduct()->getId()
        );

        // Set the display mode
        $config['product']['display'] = 'list';

        // Check the display conditions
        $condition = $config['guest']['show_guest_button']
        && $config['general']['enabled']
        && $config['products']['product_list']
        && $this->purchaseHelper->canDisplayButton();
       
        return $condition ? $config : null;
    }

    /**
     * Get the current product.
     */
    public function getProduct()
    {
        return parent::getProduct();
    }

    /**
     * Check if a product box should be displayed.
     */
    public function showProductBox()
    {
        return false;
    }
}
