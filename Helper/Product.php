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
     * @var ProductFactory
     */
    public $productFactory; 

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
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
    ) {
        $this->registry = $registry;
        $this->formKey = $formKey;
        $this->imageHelper = $imageHelper;
        $this->priceHelper = $priceHelper;
        $this->request = $request;
        $this->productFactory = $productFactory;
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
                'is_list' => $this->isListView(),
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
    public function isListView()
    {
        return !$this->isProduct(
            $this->registry->registry('current_product')
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
        return $this->productFactory->create()->load($productId);
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
    public function isProduct($product)
    {
        return $product && (int) $product->getId() > 0;
    }
}
