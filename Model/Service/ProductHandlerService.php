<?php
namespace Naxero\AdvancedInstantPurchase\Model\Service;

/**
 * Class ProductHandlerService.
 */
class ProductHandlerService
{
    /**
     * @var SearchCriteriaBuilder
     */
    public $searchCriteriaBuilder;

    /**
     * @var ProductRepository
     */
    public $productRepository;

    /**
     * ProductHandlerService constructor.
     */
    public function __construct(
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
    }

    /**
     * Check if a product is found.
     */
    public function isProductFound($productId) {
        // Search criteria
        $this->searchCriteriaBuilder->addFilter(
            'id',
            $productId
        );

        // Create the search instance
        $search = $this->searchCriteriaBuilder->create();
    
        // Get the search result
        $productList = $this->productRepository
            ->getList($search)
            ->setPageSize(1)
            ->getLastItem();

        return count($productList) > 0;
    }
}
