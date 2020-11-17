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

namespace Naxero\BuyNow\Model\Config\Backend\Source;

/**
 * Class CategoryList
 */
class CategoryList implements \Magento\Framework\Option\ArrayInterface
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
     * CategoryList constructor.
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
    ) {
        $this->storeManager = $storeManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }
    
    public function getCategories()
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
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getCategories();
    }
}
