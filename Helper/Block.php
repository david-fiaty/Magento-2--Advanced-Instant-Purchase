<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

/**
 * Class Block helper.
 */
class Block extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Get block tags in content.
     */
    public function getBlockTags($subject, $html) {
        // Find all block tag matches
        $matches = $this->findBlockTags($html);

        return $this->outputHasTags($matches, $subject)
        ? $matches : null;
    }

    /**
     * Check if a content has block tags.
     */
    public function outputHasTags($matches, $subject) {
        // Get the target class name to exclude
        $className = get_class($subject);

        // Check if the current content output has valid tags
        return !empty($matches) && !empty($matches[0])
        && strpos($className, '\\BlockButton\\') === false
        && is_array($matches[0])
        && count($matches[0]) > 0;
    }

    /**
     * Find block tags in content.
     */
    public function findBlockTags($html) {
        preg_match_all(
            $this->getSearchPattern(),
            $html,
            $matches
        );

        return $matches;
    }

    /**
     * Get the block tag search patern.
     */
    public function getSearchPattern() {
        return '/\{BuyNow(.*)\}/';
    }

    /**
     * Build a base purchase block button.
     */
    public function buildButtonBlock($subject) {
        return $subject->getLayout()
        ->createBlock('Naxero\AdvancedInstantPurchase\Block\Button\BlockButton')
        ->setTemplate('Naxero_AdvancedInstantPurchase::button/base.phtml');
    }
}