<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

use Naxero\AdvancedInstantPurchase\Model\Config\Naming;

/**
 * Class Block helper.
 */
class Block extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Customer
     */
    public $customerHelper;

    /**
     * @var Config
     */
    public $configHelper;

    /**
     * @var Product
     */
    public $productHelper;

    /**
     * Block helper class constructor.
     */
    public function __construct(
        \Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Product $productHelper
    ) {
        $this->customerHelper = $customerHelper;
        $this->configHelper = $configHelper;
        $this->productHelper = $productHelper;
    }

    /**
     * Can the button be displayed for out of stock products.
     */
    public function bypassOos($productId)
    {
        return !$this->productHelper->isInStock($productId)
        ? $this->value('buttons/bypass_oos')
        : true;
    }

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
        ->createBlock(Naming::getModulePath() . '\Block\Button\BlockButton')
        ->setTemplate(Naming::getModuleName() . '::button/base.phtml');
    }

    /**
     * Get a block configuration parameters.
     */
    public function getConfig($productId, $display) {
        // Get the config values
        $config = $this->configHelper->getValues();
        unset($config['card_form']);
        $config['ui']['loader'] = $this->configHelper->getLoaderIconUrl();
        $config['ui']['css'] = $this->configHelper->getCssPath();
        $config['module']['title'] = Naming::getModuleTitle();

        return $config
        + $this->configHelper->getValues()
        + ['product' => $this->productHelper->getData($productId, $display)]
        + $this->customerHelper->getUserParams();
    }
}