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

/**
 * Class Product helper.
 */
class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
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

        var_dump($product->getTierPrice());
exit();
        
        if ($product) {
            // Prepare the base data
            $output = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $this->getProductPrice($productId),
                'is_free' => $this->isFree($productId),
                'form_key' => $this->toolsHelper->getFormKey(),
                'has_parents' => $this->hasParents($product),
                'in_stock' => $this->isInStock($productId),
                'quantity_limits' => $this->getQuantityLimits($productId),
                'has_options' => (bool) $this->hasOptions($productId),
                'button_id' => $this->getButtonId($productId),
                'button_container_selector' => '#nbn-' . $productId,
                'button_selector' => '#' . $this->getButtonId($productId),
                'images' => $this->getProductImages($productId),
                'page_url' => $product->getProductUrl(),
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
     * Check if a product has parent products.
     */
    public function hasParents($product)
    {
        return !empty($product->getTypeInstance()->getParentIdsByChild($product->getId()));
    }

    /**
     * Get a product options.
     */
    public function getOptions($productId)
    {
        // Get the options array
        $optionsArray = $this->productTypeConfigurable->getConfigurableAttributesAsArray(
            $this->getProduct($productId)
        );

        // Add extra fields to each option
        $output = [];
        foreach ($optionsArray as $key => $option) {
            // Product id
            $option['product_id'] = $productId;

            // Option id
            $option['option_id'] = $key;

            // Attribute type info
            $option = $this->attributeHelper->addAttributeData($option);

            // Add the extra fields
            $output[] = $option;
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
    public function getProducts($categoryId = null)
    {
        // Todo - Add the category filter to collection
        $items = [];
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->setStore($this->storeManager->getStore());
        $collection->addAttributeToFilter('status', Status::STATUS_ENABLED);
        if ($categoryId && (int) $categoryId > 0) {
            $collection->addCategoriesFilter(['in' => [$categoryId]]);
        }
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
    public function getProductPrice($productId)
    {
        return $this->priceHelper->currency(
            $this->getProduct($productId)->getFinalPrice(),
            true,
            false
        );
    }

    /**
     * Get the current product image url.
     */
    public function getProductImages($productId)
    {
        // Get the product
        $product = $this->getProduct($productId);

        // Add the main image data
        $output = [
            'small' => $this->imageHelper->init($product, 'product_page_image_small')->getUrl(),
            'medium' => $this->imageHelper->init($product, 'product_page_image_medium')->getUrl(),
            'large' => $this->imageHelper->init($product, 'product_page_image_large')->getUrl(),
            'gallery' => []
        ];

        // Add the media gallery images data
        $galleryImages = $product->getMediaGalleryImages();
        if ($galleryImages && !empty($galleryImages)) {
            foreach ($galleryImages as $galleryImage) {
                $output['gallery'][] = $galleryImage->getData();
            }

            // Sort by position field
            usort($output['gallery'], function($a, $b) {
                $val1 = (int) $a['position'];
                $val2 = (int) $b['position'];

                if ($val1 == $val2) return 0;
                return $val1 < $val2 ? -1 : 1;
            });
        }

        return $output;
    }

    /**
     * Check if a product exists.
     */
    public function isProduct($productId)
    {
        $product = $this->getProduct($productId);

        return $product && (int) $product->getId() > 0;
    }
}
