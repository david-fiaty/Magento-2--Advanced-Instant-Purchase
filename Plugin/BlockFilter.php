<?php

namespace Naxero\AdvancedInstantPurchase\Plugin;

class BlockFilter
{
    public function afterGetItemHtml($subject, $result)
    {
        // Search tag
        $tag = '{BuyNow}';

        // Get the block HTML
        $blockHtml = $subject->getLayout()
        ->createBlock('Naxero\AdvancedInstantPurchase\Block\Button\BlockButton')
        ->setTemplate('Naxero_AdvancedInstantPurchase::button/block.phtml')
        ->toHtml();;

        // Replace the tag
        $html = str_replace($tag, $blockHtml, $html);

        return $html;
    }
} 