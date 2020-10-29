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
     * ViewButton class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Naxero\AdvancedInstantPurchase\Helper\Block $blockHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        
        $this->blockHelper = $blockHelper;
    }
    
    /**
     * Get the current product.
     */
    public function getProductBox()
    {
        return $this->blockHelper->renderProductBox(
            $this->getData('content')
        );
    }
}
