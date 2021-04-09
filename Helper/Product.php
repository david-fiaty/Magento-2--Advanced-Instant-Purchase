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

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Naxero\BuyNow\Model\Config\Naming;

/**
 * Class Product helper.
 */
class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var PageFactory
     */
    public $pageFactory;
    
    /**
     * @var Configurable
     */
    public $productTypeConfigurable;

    /**
     * @var Image
     */
    public $imageHelper;

    /**
     * @var Data
     */
    public $priceHelper;

    /**
     * @var RequestInterface
     */
    public $request;

    /**
     * @var ProductFactory
     */
    public $productFactory;

    /**
     * @var StockItemRepository
     */
    public $stockItemRepository;

    /**
     * StoreManagerInterface
     */
    public $storeManager;

    /**
     * CollectionFactory
     */
    public $productCollectionFactory;

    /**
     * @var Config
     */
    public $configHelper;

    /**
     * @var Tools
     */
    public $toolsHelper;

    /**
     * @var Attribute
     */
    public $attributeHelper;

    /**
     * Class Product helper constructor.
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $productTypeConfigurable,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Naxero\BuyNow\Helper\Config $configHelper,
        \Naxero\BuyNow\Helper\Tools $toolsHelper,
        \Naxero\BuyNow\Helper\Attribute $attributeHelper
    ) {
        $this->pageFactory = $pageFactory;
        $this->productTypeConfigurable = $productTypeConfigurable;
        $this->imageHelper = $imageHelper;
        $this->priceHelper = $priceHelper;
        $this->request = $request;
        $this->productFactory = $productFactory;
        $this->stockItemRepository = $stockItemRepository;
        $this->storeManager = $storeManager;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->configHelper = $configHelper;
        $this->toolsHelper = $toolsHelper;
        $this->attributeHelper = $attributeHelper;
    }

    /**
     * Load the current product data.
     */
    public function getData($productId)
    {
        $output = [];
        $product = $this->getProduct($productId);

        if ($product) {
            // Prepare the base data
            $output = [
                'id' => $product->getId(),
                'sku' => $product->getSku(),
                'name' => $product->getName(),
                'price' => $this->renderProductPrice($productId),
                'is_free' => $this->isFree($productId),
                'form_key' => $this->toolsHelper->getFormKey(),
                'has_parents' => $this->hasParents($product),
                'in_stock' => $this->isInStock($productId),
                'quantity_limits' => $this->getQuantityLimits($productId),
                'has_attributes' => (bool) $this->hasAttributes($productId),
                'has_options' => (bool) $this->hasOptions($productId),
                'button_id' => $this->getButtonId($productId),
                'button_container_selector' => '#nbn-' . $productId,
                'button_selector' => '#' . $this->getButtonId($productId),
                'page_url' => $product->getProductUrl(),
                'attributes' => $this->getAttributes($productId),
                'options' => $this->getOptions($productId)
            ];
        }

        return $output;
    }

    /**
     * Get a block button id.
     */
    public function getButtonId($productId)
    {
        return 'nbn-button-' . $productId;
    }

    /**
     * Check if a product is free.
     */
    public function isFree($productId)
    {
        return $this->getProduct($productId)->getFinalPrice() == 0;
    }

    /**
     * Check if a product has options.
     */
    public function hasOptions($productId)
    {
        return $this->getProduct($productId)->getData('has_options');
    }

    /**
     * Check if a product has attributes.
     */
    public function hasAttributes($productId)
    {
        // Get the attributes array
        $attributesArray = $this->productTypeConfigurable->getConfigurableAttributesAsArray(
            $this->getProduct($productId)
        );

        return !empty($attributesArray);
    }

    /**
     * Check if a product has parent products.
     */
    public function hasParents($product)
    {
        return !empty($product->getTypeInstance()->getParentIdsByChild($product->getId()));
    }

    /**
     * Get a product attributes.
     */
    public function getAttributes($productId)
    {
        // Get the attributes array
        $attributesArray = $this->productTypeConfigurable->getConfigurableAttributesAsArray(
            $this->getProduct($productId)
        );

        // Add extra fields to each attribute
        $output = [];
        foreach ($attributesArray as $key => $option) {
            // Product id
            $option['product_id'] = $productId;

            // Attribute id
            $option['attribute_id'] = $key;

            // Attribute type info
            $option = $this->attributeHelper->addAttributeData($option);

            // Add the full attribute data
            $output[] = $option;
        }

        return $output;
    }

    /**
     * Get a product options.
     */
    public function getOptions($productId)
    {
        $output = [];
        $product = $this->getProduct($productId);
        $options = $product->getOptions();

        if (!empty($options)) {
            foreach ($options as $option) {
                $output[] = array_merge(
                    $option->getData(),
                    ['values' => $this->getOptionValuesData($option)]
                );
            }
        }

        return  $output;
    }

    /**
     * Get a product option values data.
     */
    public function getOptionValuesData($option)
    {
        $output = [];
        foreach ($option->getValues() as $value) {
            $output[] = array_merge(
                $value->getData(),
                ['value_id' => $value->getId()]
            );
        }

        return $output;
    }

    /**
     * Check if a product is out of stock.
     */
    public function isInStock($productId)
    {
        return $this->stockItemRepository
        ->get($productId)
        ->getIsInStock();
    }

    /**
     * Get a product max and min quantity limits.
     */
    public function getQuantityLimits($productId)
    {
        // Min value
        $min = (int) $this->stockItemRepository->get($productId)->getMinSaleQty();
        $max = (int) $this->stockItemRepository->get($productId)->getMaxSaleQty();

        return [
            'min' => $min > 0 ?  $min : 1,
            'max' => $max > 0 ?  $max : ''
        ];
    }

    /**
     * Check if a product is out of stock.
     */
    public function getStockQuantity($productId)
    {
        return $this->stockItemRepository
        ->get($productId)
        ->getQty();
    }

    /**
     * Get a product instance.
     */
    public function getProduct($productId)
    {
        return $this->productFactory->create()->load($productId);
    }

    /**
     * Get a product collection.
     */
    public function getProducts($categoryId = 0)
    {
        // Get the product collection
        $items = [];
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');

        if ((int) $categoryId > 0) {
            $collection->addCategoriesFilter(['in' => [$categoryId]]);
        }

        // Prepare the product data
        foreach ($collection as $item) {
            $items[] = [
                'value' => $item->getId(),
                'label' => __($item->getName())
            ];
        }

        return $items;
    }

    /**
     * Get the current product price.
     */
    public function getProductPrice($productId, $currency = true, $html = false)
    {
        return $this->priceHelper->currency(
            $this->getProduct($productId)->getFinalPrice(),
            $currency,
            $html
        );
    }

    /**
     * Renders a product price.
     */
    public function renderProductPrice($productId, $productQuantity = null)
    {
        // Prepare the product quantity
        $productQuantity = (int) $productQuantity > 0
        ? $productQuantity
        : $this->getQuantityLimits($productId)['min'];

        // Get the product price
        $productPrice = $this->getProductPrice($productId, false, false);

        // Calculate the total price
        $totalPrice = $productQuantity * $productPrice;

        // Formatted price
        $formattedPrice = $this->priceHelper->currency(
            $totalPrice,
            $format = true,
            $includeContainer = true
        );

        return $formattedPrice;
    }

    /**
     * Check if a product exists.
     */
    public function isProduct($productId)
    {
        $product = $this->getProduct($productId);

        return $product && (int) $product->getId() > 0;
    }

    /**
     * Render a product price box.
     */
    public function getPriceBoxHtml($config, $productQuantity = 1)
    {
        return $this->pageFactory->create()->getLayout()
        ->createBlock(Naming::getModulePath() . '\Block\Product\Price')
        ->setTemplate(Naming::getModuleName() . '::product/price.phtml')
        ->setData('config', $config)
        ->setData('product_quantity', $productQuantity)
        ->toHtml();
    }

    /**
     * Render a product purchase button.
     */
    public function getButtonHtml($config, $layout = null)
    {
        $layout = $layout ? $layout : $this->pageFactory->create()->getLayout();
        return $layout
        ->createBlock(Naming::getModulePath() . '\Block\Button\BaseButton')
        ->setTemplate(Naming::getModuleName() . '::button/base.phtml')
        ->setData('config', $config)
        ->setData('isFree', $this->isFree($config['product']['id']))
        ->toHtml();
    }
}
