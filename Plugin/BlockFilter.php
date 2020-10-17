<?php

namespace Naxero\AdvancedInstantPurchase\Plugin;

class BlockFilter
{
    public function afterToHtml($subject, $html) {
        // Get all button tags in content
        $validation = '/\{BuyNow\}/i';
        //$tag = '{BuyNow}';
        preg_match_all(
            $validation,
            $html,
            $tags
        );

        // Replace the tags
        //foreach ($tags as $tag) {

            $className = get_class($subject);
            if (strpos($className, '\\BlockButton\\') === false) {
                $blockHtml = $subject->getLayout()
                ->createBlock('Naxero\AdvancedInstantPurchase\Block\Button\BlockButton')
                ->setTemplate('Naxero_AdvancedInstantPurchase::button/block.phtml')
                ->toHtml();

                $html = str_replace($tags[0], $blockHtml, $html);
            }
        

        //}

        return $html;
    }
} 