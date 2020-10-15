<?php

namespace Naxero\AdvancedInstantPurchase\Plugin;

class BlockFilter
{
    /**
     * @var PageFactory
     */
    public $pageFactory;

    /**
     * BlockFilter constructor.
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->pageFactory = $pageFactory;
    }

    public function afterToHtml(
        $original,
        $html
    ) {

        // Get all button tags in content
        $validation = '/\{BuyNow.*\}/i';
        preg_match_all(
            $validation,
            $html,
            $tags
        );

        // Replace the tags
        foreach ($tags as $tag) {
            $blockHtml = $this->getBlockHtml();
            $html = str_replace($tag, $blockHtml, $html);
        }
    }

    public function getBlockHtml() {
        return $this->pageFactory->create()->getLayout()
        ->createBlock('Naxero\AdvancedInstantPurchase\Block\Button\ViewButton')
        ->setTemplate('Naxero_AdvancedInstantPurchase::button/block.phtml')
        ->toHtml();
    }
} 