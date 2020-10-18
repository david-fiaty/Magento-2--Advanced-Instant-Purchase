<?php

namespace Naxero\AdvancedInstantPurchase\Plugin;

class BlockFilter
{
    public function afterToHtml($subject, $html) {
        // Prepare the tag search pattern
        $search = '/\{BuyNow(.*)\}/i';

        // Find all matches
        preg_match_all(
            $search,
            $html,
            $matches
        );
        
        // Get the target class name
        $className = get_class($subject);

        // Loop through the found tags
        $condition = !empty($matches) && !empty($matches[0])
        && strpos($className, '\\BlockButton\\') === false
        && is_array($matches[0]);
        if ($condition) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                // Prepare the product block
                $blockHtml = $subject->getLayout()
                ->createBlock('Naxero\AdvancedInstantPurchase\Block\Button\BlockButton')
                ->setTemplate('Naxero_AdvancedInstantPurchase::button/base.phtml');

                // Loop through the tag parameters
                $hasParameters = isset($matches[1])
                && isset($matches[1][$i]) && !empty($matches[1][$i]);

                if ($hasParameters) {
                    // Product id

                    /*
                    $valid = isset($param[1]) && !empty($param[1]) && (int) $param[1] > 0;
                    var_dump($param);

var_dump($valid);
*/
                    /*
                    preg_match('/product_id=(\d*)/', $matches[1][$i], $param);
                    $valid = isset($param[1]) && !empty($param[1]) && (int) $param[1] > 0;
                    if ($valid) {
                        //$subject->setData('product_id', $param[1]);
                    } 
                    */
                }

                // Render the block
                         $subject->setData('product_id', 6);

                // Replace the tag with the generated HTML
                $html = str_replace($matches[1][$i], $blockHtml->toHtml(), $html);
            }
        }

        return $html;
    }
} 