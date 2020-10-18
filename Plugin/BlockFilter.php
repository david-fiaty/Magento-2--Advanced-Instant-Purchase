<?php

namespace Naxero\AdvancedInstantPurchase\Plugin;

/**
 * Class BlockFilter.
 */
class BlockFilter
{
    /**
     * Block
     */
    public $blockHelper;

    /**
     * Class BlockFilter constructor.
     */
    public function __construct(
        \Naxero\AdvancedInstantPurchase\Helper\Block $blockHelper
    ) {
        $this->blockHelper = $blockHelper;
    }

    /**
     * After to HTML plugin event handler.
     */
    public function afterToHtml($subject, $html) {
        // Find block tags in the content
        $matches = $this->blockHelper->getBlockTags($subject, $html);

        // Process tags found
        if ($matches) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                // Error count for each tag found
                $errors = 0;

                // Loop through the tag parameters
                if ($this->tagHasParameters($matches, $i)) {
                    // Product id
                    preg_match('/product_id=(\d*)/', $matches[1][$i], $param);
                    $valid = isset($param[1]) && !empty($param[1]) && (int) $param[1] > 0;
                    if ($valid) {
                        // Build the block
                        $blockHtml = $this->blockHelper->buildButtonBlock($subject);

                        // Set the product id block parameter
                        $blockHtml->setData('product_id', $param[1]);

                        // Replace the tag with the generated HTML
                        $html = str_replace($matches[1][$i], $blockHtml->toHtml(), $html);
                    } 
                    else {
                        $errors++;
                    }
                }

                // Handle the errors
                //$errors;
            }
        }

        return $html;
    }

    /**
     * Check if a block tag has parameters.
     */
    public function tagHasParameters($matches, $i) {
        return isset($matches[1]) && isset($matches[1][$i]) && !empty($matches[1][$i]);
    }
} 