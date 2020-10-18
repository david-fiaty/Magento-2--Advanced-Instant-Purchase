<?php

namespace Naxero\AdvancedInstantPurchase\Plugin;

class BlockFilter
{
    public function afterToHtml($subject, $html) {
        $matches = $this->getBlockTags($subject, $html);
        if ($matches) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                // Prepare the product block
                $blockHtml = $subject->getLayout()
                ->createBlock('Naxero\AdvancedInstantPurchase\Block\Button\BlockButton')
                ->setTemplate('Naxero_AdvancedInstantPurchase::button/base.phtml');

                // Loop through the tag parameters
                if ($this->hasParameters($matches, $i)) {
                    // Product id
                    preg_match('/product_id=(\d*)/', $matches[1][$i], $param);

                    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
$logger = new \Zend\Log\Logger();
$logger->addWriter($writer);
$logger->info(var_dump($param));

                    $valid = isset($param[1]) && !empty($param[1]) && (int) $param[1] > 0;
                    if ($valid) {
                        $blockHtml->setData('product_id', $param[1]);
                    } 
                }

                // Replace the tag with the generated HTML
                $html = str_replace($matches[1][$i], $blockHtml->toHtml(), $html);
            }

        }

        return $html;
    }

    public function getBlockTags($subject, $html) {
        // Get the target class name
        $className = get_class($subject);

        // Prepare the tag search pattern
        $search = '/\{BuyNow(.*)\}/i';

        // Find all matches
        preg_match_all(
            $search,
            $html,
            $matches
        );

        // Loop through the found tags
        $condition = !empty($matches) && !empty($matches[0])
        && strpos($className, '\\BlockButton\\') === false
        && is_array($matches[0])
        && count($matches[0]) > 0;

        return $condition ? $matches : null;
    }

    public function hasParameters($matches, $i) {
        return isset($matches[1])
        && isset($matches[1][$i]) && !empty($matches[1][$i]);
    }

} 