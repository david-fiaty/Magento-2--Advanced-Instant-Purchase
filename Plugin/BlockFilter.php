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

        // Loop through the found tags
        $condition = !empty($tags) && strpos($className, '\\BlockButton\\') === false;
        if ($condition) {
            foreach ($tags as $tag) {
                // Get the class name
                $className = get_class($subject);

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