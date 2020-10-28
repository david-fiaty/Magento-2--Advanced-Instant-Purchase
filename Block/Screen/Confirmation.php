<?php
namespace Naxero\AdvancedInstantPurchase\Block\Screen;

/**
 * Confirmation class constructor.
 */
class Confirmation extends \Magento\Framework\View\Element\Template
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
        \Naxero\AdvancedInstantPurchase\Helper\Product $productHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        
        $this->productHelper = $productHelper;
    }
    
    /**
     * Get the current product.
     */
    public function getProductBox()
    {
        return $this->productHelper->renderProductBox($this);
    }
}
