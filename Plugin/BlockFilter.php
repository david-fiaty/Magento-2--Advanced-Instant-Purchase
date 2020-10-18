<?php

namespace Naxero\AdvancedInstantPurchase\Plugin;

class BlockFilter
{
    public function afterToHtml($subject, $html) {
        // Find block tags in the content
        $matches = $this->getBlockTags($subject, $html);

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
                        $blockHtml = $this->buildBlock($subject);

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

    public function getBlockTags($subject, $html) {
        // Find all block tag matches
        $matches = $this->findTags($html);

        return $this->outputHasTags($matches, $subject)
        ? $matches : null;
    }

    public function findTags($html) {
        preg_match_all(
            $this->getSearchPattern(),
            $html,
            $matches
        );

        return $matches;
    }

    public function getSearchPattern() {
        return '/\{BuyNow(.*)\}/';
    }

    public function outputHasTags($matches, $subject) {
        // Get the target class name to exclude
        $className = get_class($subject);

        // Check if the current content output has valid tags
        return !empty($matches) && !empty($matches[0])
        && strpos($className, '\\BlockButton\\') === false
        && is_array($matches[0])
        && count($matches[0]) > 0;
    }

    public function tagHasParameters($matches, $i) {
        return isset($matches[1]) && isset($matches[1][$i]) && !empty($matches[1][$i]);
    }

    public function buildBlock($subject) {
        return $subject->getLayout()
        ->createBlock('Naxero\AdvancedInstantPurchase\Block\Button\BlockButton')
        ->setTemplate('Naxero_AdvancedInstantPurchase::button/base.phtml');
    }
} 