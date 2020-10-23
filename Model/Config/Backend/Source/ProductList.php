<?php
namespace Naxero\AdvancedInstantPurchase\Model\Config\Backend\Source;

/**
 * Class PaymentMethods
 */
class ProductList implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * CollectionFactory
     */
    public $productCollectionFactory;
        
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory       
    ) {    
        $this->productCollectionFactory = $productCollectionFactory;    
    }
    
    public function getProducts()
    {
        $items = [];
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
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
        return $this->getProducts();
    }
}
