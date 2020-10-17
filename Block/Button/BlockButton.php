<?php
namespace Naxero\AdvancedInstantPurchase\Block\Button;

/**
 * BlockButton class constructor.
 */
class BlockButton extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Config
     */
    public $configHelper;

    /**
     * @var Customer
     */
    public $customerHelper;

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
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Purchase $purchaseHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Product $productHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        
        $this->configHelper = $configHelper;
        $this->purchaseHelper = $purchaseHelper;
        $this->customerHelper = $customerHelper;
        $this->productHelper = $productHelper;
    }

    /**
     * Get the block config.
     */
    public function getConfig()
    {
        $config = $this->configHelper->getValues();
        $condition = $config['guest']['show_guest_button']
        && $config['general']['enabled']
        && $config['products']['product_view']
        && !$this->productHelper->isListView();

        return $condition ? $config : null;
    }
    
    /**
     * Get the current product.
     */
    public function getProduct()
    {
        return $this->productHelper->getProduct();
    }

    /**
     * Disable the block cache.
     */
    public function getCacheLifetime()
    {
        return null;
    }
}