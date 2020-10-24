<?php
namespace Naxero\AdvancedInstantPurchase\Model\Service;


/**
 * Class ProductHandlerService.
 */
class ProductHandlerService
{
    /**
     * @var FilterBuilder
     */
    public $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    public $filterGroupBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    public $searchCriteriaBuilder;

    /**
     * @var ProductRepository
     */
    public $productRepository;

    /**
     * CardHandlerService constructor.
     */
    public function __construct(
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
    }

    public function isProductFound($productId) {
        // Filter
        $filter = $this->filterBuilder->setField('id')
        ->setConditionType('equals')
        ->setValue($productId)    
        ->create();

        // Filter goup
        $filterGroup = $this->filterGroupBuilder
        ->addFilter($filter)
        ->create();

        // Search criteria
        $searchCriteria = $this->searchCriteriaBuilder
        ->setFilterGroups([$filterGroup])
        ->create();
    
        // Get the product list
        $productList = $this->productRepository->getList($searchCriteria)->getItems();  
    
        return count($productList) > 0;
    }
}
