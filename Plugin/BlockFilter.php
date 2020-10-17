<?php

namespace Naxero\AdvancedInstantPurchase\Plugin;

class BlockFilter
{
    public function afterToHtml($subject, $html) {
        // Prepare the tag search pattern
        $search = '/\{BuyNow\}/i';

        // Find all matches
        preg_match_all(
            $search,
            $html,
            $tags
        );

        // Get the target class name
        $className = get_class($subject);
        
        // Loop through the found tags
        $condition = !empty($tags) && !empty($tags[0])
        && strpos($className, '\\BlockButton\\') === false;
        if ($condition) {
            foreach ($tags[0] as $tag) {
                // Process the blocks output
                $blockHtml = $subject->getLayout()
                ->createBlock('Naxero\AdvancedInstantPurchase\Block\Button\BlockButton')
                ->setTemplate('Naxero_AdvancedInstantPurchase::button/block.phtml')
                ->toHtml();

                // Replace the tag with the generated HTML
                $html = str_replace($tags[0], $blockHtml, $html);
            }
        }

        return $html;
    }
} 