<?php
namespace Naxero\BuyNow\Block\Screen;

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
        \Naxero\BuyNow\Helper\Block $blockHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->blockHelper = $blockHelper;
    }
}
