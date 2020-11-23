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

namespace Naxero\BuyNow\Block\Button;

use Naxero\BuyNow\Model\Config\Naming;

/**
 * WidgetButton class.
 */
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class WidgetButton extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    const MODE = 'widget';

    public $_template = "button/base.phtml";

    /**
     * @var Repository
     */
    public $assetRepository;

    /**
     * @var Block
     */
    public $blockHelper;

    /**
     * @var Config
     */
    public $configHelper;

    /**
     * @var Purchase
     */
    public $purchaseHelper;

    /**
     * @var Product
     */
    public $productHelper;

    /**
     * @var Category
     */
    public $categoryHelper;

    /**
     * WidgetButton class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\View\Asset\Repository $assetRepository,
        \Naxero\BuyNow\Helper\Block $blockHelper,
        \Naxero\BuyNow\Helper\Config $configHelper,
        \Naxero\BuyNow\Helper\Purchase $purchaseHelper,
        \Naxero\BuyNow\Helper\Product $productHelper,
        \Naxero\BuyNow\Helper\Category $categoryHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        
        $this->assetRepository = $assetRepository;
        $this->blockHelper = $blockHelper;
        $this->configHelper = $configHelper;
        $this->purchaseHelper = $purchaseHelper;
        $this->productHelper = $productHelper;
        $this->categoryHelper = $categoryHelper;
    }

    /**
     * Get the block config.
     */
    public function getConfig()
    {
        // Prepare the config
        $config = $this->blockHelper->getConfig(
            $this->getProduct()->getId()
        );

        // Set the display mode
        $config['product']['display'] = self::MODE;

        // Check the display conditions
        $condition = $this->purchaseHelper->canDisplayButton();
        if ($condition) {
            // Update the product attributes data
            $config = $this->updateAttributesData($config);

            // Update the config with tag parameters
            $config = $this->updateWidgetConfig($config);

            return $config;
        }
        
        return null;
    }
    
    /**
     * Get the current product.
     */
    public function getProduct()
    {
        // Prepare the variables
        $selectionMode = $this->getData('product_selection_mode');
        $productId = $this->getData('product_id');

        // Handle the category case
        if ($selectionMode == 'category') {
            // Prepare teh parameters
            $productFilter = $this->getData('product_filter');
            $categoryId = $this->getData('category_id');

            // Get the product filter function
            $fn = 'get';
            $members = explode('_', $productFilter);
            foreach ($members as $member) {
                $fn .= ucfirst($member);
            }
            $fn .= 'Product';

            // Update the product id
            $product = $this->categoryHelper->$fn($categoryId);
            if ($product) {
                $productId = $product->getId();
            }
        }

        return $this->productHelper->getProduct($productId);     
    }

    /**
     * Update the product attributes data.
     */
    public function updateAttributesData($config)
    {
        return $this->blockHelper->updateAttributesData($config, true);
    }

    /**
     * Update the config with tag parameters.
     */
    public function updateWidgetConfig($config)
    {
        // Get the XML config fields
        $configFields = $this->array_keys_recursive(
            $this->configHelper->getConfigFields()
        );

        // Get the block data
        $blockData = $this->getData();

        // Loop through the available config fields
        foreach ($configFields as $group => $fields) {
            foreach ($fields as $i => $field) {
                if (array_key_exists($field, $blockData)) {
                    if (!empty($blockData[$field])) {
                        $config[$group][$field] = $this->configHelper->toBooleanFilter(
                            $blockData[$field]
                        );
                    }
                }   
            }
        }

        return $config;
    }

    /**
     * Get array keys recursively.
     */
    public function array_keys_recursive(array $array) : array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $index[$key] = $this->array_keys_recursive($value);
            } else {
                $index[]= $key;
            }
        }
    
        return $index ?? [];
    }

    /**
     * Load additional scripts.
     */
    public function getJsAssets()
    {
        return [
            $this->assetRepository->createAsset(
                Naming::getModuleName() . '::js/lib/elevate/jquery.elevatezoom.js'
            )
        ];
    }
    
    /**
     * Disable the block cache.
     */
    public function getCacheLifetime()
    {
        return null;
    }
}
