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

/**
 * Class Category helper.
 */
class Category extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * StoreManagerInterface
     */
    public $storeManager;

    /**
     * CollectionFactory
     */
    public $categoryCollectionFactory;

    /**
     * Tree
     */
    public $categoryTree;

    /**
     * CollectionFactory
     */
    public $productCollectionFactory;

    /**
     * @var StockItemRepository
     */
    public $stockItemRepository;

    /**
     * @var CollectionFactory
     */
    public $bestSellersCollectionFactory;

    /**
     * @var Product
     */
    public $productHelper;

    /**
     * Class Category helper constructor.
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Block\Adminhtml\Category\Tree $categoryTree,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $bestSellersCollectionFactory,
        \Naxero\BuyNow\Helper\Product $productHelper
    ) {
        $this->storeManager = $storeManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryTree = $categoryTree;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->stockItemRepository = $stockItemRepository;
        $this->bestSellersCollectionFactory = $bestSellersCollectionFactory;
        $this->productHelper = $productHelper;
    }

    /**
     * Get the catalog categories tree.
     */
    public function getCategoryTree($categoryId = 0, $output = [], $i = 0)
    {
        $categories = $this->getCategories($categoryId);
        if (!empty($categories)) {
            foreach ($categories as $category) {
                // Load the category product count
                $categoryProductCount = $this->getProductCollection($category['id'])->count();

                // Add the category
                $output[] = [
                    'id' => $category['id'],
                    'name' => $category['text'],
                    'level' => $i,
                    'has_products' => $categoryProductCount > 0
                ];

                // Check subcategories recursively
                $children = $category->getChildren();
                if ($children && !empty($children)) {
                    return $this->getCategories($children, $output, $i + 1);
                }
            }
        }

        return $output;
    }

    /**
     * Get the catalog root categories.
     */
    public function getCategories($categoryId = 0)
    {
        $items = [];
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        if ($categoryId > 0) {
            $collection->addAttributeToFilter(
                'parent_id',
                ['eq' => $categoryId]
            );
        }

        return $collection;
    }

    /**
     * Get a product collection in category.
     */
    public function getProductCollection($categoryId)
    {
        return $this->productCollectionFactory->create()
            ->addCategoriesFilter(['in' => $categoryId])
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
            ->setStore($this->storeManager->getStore());
    }

    /**
     * Get the lowest stock product.
     */
    public function getLowestStockProduct($categoryId)
    {
        $collection = $this->getProductCollection($categoryId)->addAttributeToSelect('entity_id');
        $productIds = array_keys($collection->getItems());
        $stockValues = [];
        foreach ($productIds as $productId) {
            $stockValues[] = [
                'product_id' => $productId,
                'stock_quantity' => $this->stockItemRepository->get($productId)->getQty()
            ];
        }

        usort($stockValues, function ($a, $b) {
            $val1 = (int) $a['stock_quantity'];
            $val2 = (int) $b['stock_quantity'];

            if ($val1 == $val2) {
                return 0;
            }
            return $val1 < $val2 ? -1 : 1;
        });

        return $this->productHelper->getProduct($stockValues[0]['product_id']);
    }

    /**
     * Get the highest stock product.
     */
    public function getHighestStockProduct($categoryId)
    {
        $collection = $this->getProductCollection($categoryId)->addAttributeToSelect('entity_id');
        $productIds = array_keys($collection->getItems());
        $stockValues = [];
        foreach ($productIds as $productId) {
            $stockValues[] = [
                'product_id' => $productId,
                'stock_quantity' => $this->stockItemRepository->get($productId)->getQty()
            ];
        }

        usort($stockValues, function ($a, $b) {
            $val1 = (int) $a['stock_quantity'];
            $val2 = (int) $b['stock_quantity'];

            if ($val1 == $val2) {
                return 0;
            }
            return $val1 < $val2 ? -1 : 1;
        });

        return $this->productHelper->getProduct($stockValues[count($stockValues) - 1]['product_id']);
    }

    /**
     * Get the lowest price product.
     */
    public function getLowestPriceProduct($categoryId)
    {
        $products = $this->getProductCollection($categoryId)->addAttributeToSelect('entity_id');
        $priceValues = [];
        foreach ($products as $product) {
            $priceValues[] = [
                'product_id' => $product->getId(),
                'final_price' => $product->getFinalPrice()
            ];
        }

        usort($priceValues, function ($a, $b) {
            $val1 = (float) $a['final_price'];
            $val2 = (float) $b['final_price'];

            if ($val1 == $val2) {
                return 0;
            }
            return $val1 < $val2 ? -1 : 1;
        });

        return $this->productHelper->getProduct($priceValues[0]['product_id']);
    }

    /**
     * Get the highest price product.
     */
    public function getHighestPriceProduct($categoryId)
    {
        $products = $this->getProductCollection($categoryId)->addAttributeToSelect('entity_id');
        $priceValues = [];
        foreach ($products as $product) {
            $priceValues[] = [
                'product_id' => $product->getId(),
                'final_price' => $product->getFinalPrice()
            ];
        }

        usort($priceValues, function ($a, $b) {
            $val1 = (float) $a['final_price'];
            $val2 = (float) $b['final_price'];

            if ($val1 == $val2) {
                return 0;
            }
            return $val1 < $val2 ? -1 : 1;
        });

        return $this->productHelper->getProduct($priceValues[count($priceValues) - 1]['product_id']);
    }

    /**
     * Get the latest product.
     */
    public function getLatestProduct($categoryId)
    {
        return $this->getProductCollection($categoryId)
            ->setOrder('entity_id', 'DESC')
            ->getFirstItem();
    }

    /**
     * Get the oldest product.
     */
    public function getOldestProduct($categoryId)
    {
        return $this->getProductCollection($categoryId)
            ->setOrder('entity_id', 'ASC')
            ->getFirstItem();
    }

    /**
     * Get a random product.
     */
    public function getRandomProduct($categoryId)
    {
        $collection = $this->getProductCollection($categoryId)->addAttributeToSelect('entity_id');
        if ($collection->count() > 0) {
            $productIds = array_keys($collection->getItems());
            $productId = array_rand($productIds);
            return $this->productHelper->getProduct($productId);
        }

        return null;
    }

    /**
     * Get the highest sales product.
     */
    public function getHighestSalesProduct($categoryId)
    {
        $collection = $this->bestSellersCollectionFactory->create()->setPeriod('year');
        if ($collection->count() > 0) {
            $productIds = array_keys($collection);
            return $this->productHelper->getProduct($productIds[0]);
        }

        return null;
    }

    /**
     * Get the lowest sales product.
     */
    public function getLowestSalesProduct($categoryId)
    {
        $collection = $this->bestSellersCollectionFactory->create()->setPeriod('year');
        if ($collection->count() > 0) {
            $productIds = array_keys($collection);
            return $this->productHelper->getProduct($productIds[count($productIds) - 1]);
        }

        return null;
    }
}
