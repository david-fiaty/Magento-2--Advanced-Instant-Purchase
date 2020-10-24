<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

/**
 * Class Product helper.
 */
class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Registry
     */
    public $registry; 

    /**
     * @var FormKey
     */
    public $formKey;

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
     * @var ProductRepository
     */
    public $productRepository; 

    /**
     * @var StockItemRepository
     */
    public $stockItemRepository; 

    /**
     * Class Product helper constructor.
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
    ) {
        $this->registry = $registry;
        $this->formKey = $formKey;
        $this->imageHelper = $imageHelper;
        $this->priceHelper = $priceHelper;
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->stockItemRepository = $stockItemRepository;
    }

    /**
     * Load the current product data.
     */
    public function getData($productId)
    {
        $output = [];        
        $product = $this->getProduct($productId);
        if ($product) {
            $output = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $this->getProductPrice($productId),
                'is_free' => $this->isFree($productId),
                'url' => $this->getProductImageUrl($productId),
                'form_key' => $this->getFormKey(),
                'in_stock' => $this->isInStock($productId),
                'has_options' => (bool) $this->hasOptions($productId),
                'button_id' => $this->getButtonId($productId),
                'button_selector' => '#' . $this->getButtonId($productId),
                'page_url' => $product->getProductUrl()
            ];
        }

        return $output;
    }

    /**
     * Get a block button id.
     */
    public function getButtonId($productId) {
        return 'aip-button-' . $productId;
    }

    /**
     * Check if the product is in a list view.
     */
    public function isPageView()
    {
        return $this->isProduct(
            $this->registry->registry('current_product')->getId()
        );
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
     * Check if a product is out of stock.
     */
    public function isInStock($productId)
    {
        return $this->stockItemRepository
        ->get($productId)
        ->getIsInStock();
    }

    /**
     * Get a product instance.
     */
    public function getProduct($productId)
    {
        return $this->productRepository->getById($productId);
    }

    /**
     * Get a product form key.
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
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
    public function getProductImageUrl($productId)
    {
        return $this->imageHelper->init(
            $this->getProduct($productId),
            'product_base_image'
        )->constrainOnly(FALSE)
        ->keepAspectRatio(TRUE)
        ->keepFrame(FALSE)
        ->getUrl();
    }

    /**
     * Check if a product exists.
     */
    public function isProduct($productId)
    {
        return (int) $this->isProductFound($productId)
        && $this->isProductIdValid($productId);
    }

    /**
     * Check if a product is found.
     */
    public function isProductFound($productId)
    {
        return $this->getProduct($productId);
    }

    /**
     * Check if a product id is valid.
     */
    public function isProductIdValid($productId)
    {
        return (int) $productId > 0;
    }
}
