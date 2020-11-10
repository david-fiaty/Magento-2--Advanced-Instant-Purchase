<?php
/**
 * Naxero.com
 * Professional ecommerce integrations for Magento.
 *
 * PHP version 7
 *
 * @category  Magento2
 * @package   Naxero
 * @author    Platforms Development Team <contact@naxero.com>
 * @copyright © Naxero.com all rights reserved
 * @license   https://opensource.org/licenses/mit-license.html MIT License
 * @link      https://www.naxero.com
 */

namespace Naxero\BuyNow\Helper;

use Naxero\BuyNow\Model\Config\Naming;

/**
 * Class Block helper.
 */
class Block extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var PageFactory
     */
    public $pageFactory;

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
     * @var FilterHandlerService
     */
    public $filterHandler;

    /**
     * Block helper class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Naxero\BuyNow\Helper\Customer $customerHelper,
        \Naxero\BuyNow\Helper\Config $configHelper,
        \Naxero\BuyNow\Helper\Product $productHelper,
        \Naxero\BuyNow\Model\Service\FilterHandlerService $filterHandler
    ) {
        $this->pageFactory = $pageFactory;
        $this->customerHelper = $customerHelper;
        $this->configHelper = $configHelper;
        $this->productHelper = $productHelper;
        $this->filterHandler = $filterHandler;
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
    public function getBlockTags($subject, $html)
    {
        // Find all block tag matches
        $matches = $this->findBlockTags($html);

        return $this->outputHasTags($matches, $subject)
        ? $matches : null;
    }

    /**
     * Check if a content has block tags.
     */
    public function outputHasTags($matches, $subject)
    {
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
    public function findBlockTags($html)
    {
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
    public function getSearchPattern()
    {
        return '/\{BuyNow(.*)\}/';
    }

    /**
     * Build a base purchase block button.
     */
    public function buildButtonBlock($subject)
    {
        return $subject->getLayout()
        ->createBlock(Naming::getModulePath() . '\Block\Button\BlockButton')
        ->setTemplate(Naming::getModuleName() . '::button/base.phtml');
    }

    /**
     * Get a block configuration parameters.
     */
    public function getConfig($productId)
    {
        // Get the config values
        $config = $this->configHelper->getValues();

        // Prepare the block config data
        $output = $config
        + $this->configHelper->getValues()
        + ['product' => $this->productHelper->getData($productId)]
        + $this->customerHelper->getUserParams();

        // Prepare the block parameters
        $output = $this->prepareBlockConfig($output);

        return $output;
    }

    /**
     * Prepare the block config parameters.
     */
    public function prepareBlockConfig($config)
    {
        // Remove the card form
        unset($config['card_form']);

        // Prepare the UI loader
        $config['ui']['loader'] = $this->configHelper->getLoaderIconUrl();
        $config['ui']['css'] = $this->configHelper->getCssPath();

        // Module title
        $config['module']['title'] = Naming::getModuleTitle();

        // Module route
        $config['module']['route'] = Naming::getModuleRoute();

        // Prepare the popup window title
        $config['popups']['popup_title'] = $this->filterHandler
        ->filterContent($config['popups']['popup_title'], $config);

        return $config;
    }

    /**
     * Render a product box.
     */
    public function renderProductBox($productId, $subject = null)
    {
        // Get the layout
        $layout = $subject
        ? $subject->getLayout()
        : $this->pageFactory->create()->getLayout();

        return $layout
        ->createBlock('Magento\Framework\View\Element\Template')
        ->setTemplate(Naming::getModuleName() . '::product/box.phtml')
        ->setData('content', $this->getConfig($productId))
        ->toHtml();
    }

    /**
     * Update the product attributes data.
     */
    public function updateAttributesData($config, $force = false)
    {
        // Prepare parameters
        $swatchAsSelect = $config['products']['swatch_as_select'];
        $updatedOptions = [];

        // Update the attribute display parameters
        if ($config['product']['has_options']) {
            foreach ($config['product']['options'] as $option) {
                $isSwatch = $option['attribute_type'] == 'swatch';
                if ($isSwatch && ($swatchAsSelect || $force)) {
                    $option['attribute_type'] = 'select';
                }
                
                $updatedOptions[] = $option;
            }

            $config['product']['options'] = $updatedOptions;
            
            return $config;
        }
    }
}
