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
     * Class Category helper constructor.
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Block\Adminhtml\Category\Tree $categoryTree     
    ) {
        $this->storeManager = $storeManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryTree = $categoryTree; 
    }

   /**
     * Get the catalog categories.
     */
    public function getCategories($categories = null, $output = [], $i = 0) {
        $categories = $categories ?? $this->getTree();
        if (!empty($categories)) {
            foreach ($categories as $category) {
                // Add the category
                $output[] = [
                    'id' => $category['id'],
                    'name' => $category['text'],
                    'level' => $i
                ];

                // Check subcategories recursively
                $condition = isset($category['children']) && is_array($category['children']) && !empty($category['children']);
                if ($condition) {
                    $children = $this->getCategories($category['children'], $output, $i);
                    $i++;

                    return $children;
                }

            }
        }

        return $output;
    }

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

    public function getTree()
    {
        return $this->categoryTree->getTree(); 
    }
}
