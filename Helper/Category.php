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
     * Get the catalog categories.
     */
    public function getCategories($categories = null, $output = [], $i = 0) {
        $categories = $categories ?? $this->getTree();
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
                $condition = isset($category['children']) && is_array($category['children']) && !empty($category['children']);
                if ($condition) {
                    $i++;
                    $children = $this->getCategories($category['children'], $output, $i);
                    return $children;
                }

            }
        }

        return $output;
    }

    /**
     * Get the catalog root categories.
     */
    public function getRootCategories()
    {
        $items = [];
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->setStore($this->storeManager->getStore());
        foreach ($collection as $item) {
            $items[] = [
                'value' => $item->getId(),
                'label' => __($item->getName())
            ];
        }

        return $items;
    }

    /**
     * Get the catalog categories tree.
     */
    public function getTree()
    {
        return $this->categoryTree->getTree(); 
    }

    /**
     * Get a product collection in category.
     */
    public function getProductCollection($categoryId)
    {
        return $this->productCollectionFactory->create()
            ->addCategoriesFilter(['in' => $categoryId])
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
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
            $stockValues[]= [
                'product_id' => $productId,
                'stock_quantity' => $this->stockItemRepository->get($productId)->getQty()
            ];
        }        
        
        usort($stockValues, function($a, $b) {
            return strcmp($a['stock_quantity'], $b['stock_quantity']);
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
            $stockValues[]= [
                'product_id' => $productId,
                'stock_quantity' => $this->stockItemRepository->get($productId)->getQty()
            ];
        }        
        
        usort($stockValues, function($a, $b) {
            return strcmp($a['stock_quantity'], $b['stock_quantity']);
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
            $priceValues[]= [
                'product_id' => $product->getId(),
                'final_price' => $product->getFinalPrice()
            ];
        }     
        
        usort($priceValues, function($a, $b) {
            return strcmp($a['final_price'], $b['final_price']);
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
            $priceValues[]= [
                'product_id' => $product->getId(),
                'final_price' => $product->getFinalPrice()
            ];
        }     
        
        usort($priceValues, function($a, $b) {
            return strcmp($a['final_price'], $b['final_price']);
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
        $productIds = array_keys($collection->getItems());
        $productId = array_rand($productIds);

        return $this->productHelper->getProduct($productId); 
    }

    /**
     * Get the highest sales product.
     */
    public function getHighestSalesProduct($categoryId)
    {
        $collection = $this->bestSellersCollectionFactory->create()->setPeriod('year');
        $productIds = array_keys($collection);

        return $this->productHelper->getProduct($productIds[0]); 
    }

    /**
     * Get the lowest sales product.
     */
    public function getLowestSalesProduct($categoryId)
    {
        
        $collection = $this->bestSellersCollectionFactory->create()->setPeriod('year');
        $productIds = array_keys($collection);

        return $this->productHelper->getProduct($productIds[count($productIds) - 1]); 
    }
}
