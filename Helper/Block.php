<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

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
     * ViewButton class constructor.
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
     * Get filtered config values for the frontend.
     */
    public function getFrontendValues()
    {
        // Get the config values
        $values = $this->configHelper->getValues();

        // Remove uneeded elements
        unset($values['card_form']);

        // Product info
        $values['product'] = $this->productHelper->getData();
        $values['isListView'] = $this->productHelper->isListView();

        // Loader icon
        $values['ui']['loader'] = $this->configHelper->getLoaderIconUrl();
        
        return [
            'params' => array_merge(
                $values,
                $this->customerHelper->getUserParams()
            )
        ];
    }

    /**
     * Can the button be displayed for out of stock products.
     */
    public function bypassOos($pid)
    {
        $productId = $this->productHelper->getProduct($pid)->getId();
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
        ->createBlock('Naxero\AdvancedInstantPurchase\Block\Button\BlockButton')
        ->setTemplate('Naxero_AdvancedInstantPurchase::button/base.phtml');
    }

    /**
     * Get a block js configuration parameters.
     */
    public function getJsConfig($productId, $buttonId, $formKey) {
        return json_encode([
            'jsConfig' => array_merge(
                $this->customerHelper->getUserParams(),
                $this->buildProductarray($productId, $buttonId, $formKey),
                $this->getFrontendValues()
            )
        ]);
    }

    /**
     * Build a product array.
     */
    public function buildProductarray($productId, $buttonId, $formKey) {
        return [
            'product' => [
                'id' => $productId,
                'formKey' => $formKey,
                'buttonSelector' => '#' . $buttonId
            ]
        ];
    }
}