<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

/**
 * Class Product
 */
class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
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
     * @var Registry
     */
    public $registry; 

    /**
     * @var RequestInterface
     */
    public $request; 

    /**
     * @var ProductFactory
     */
    public $productFactory; 

    /**
     * Class Customer constructor.
     */
    public function __construct(
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->formKey = $formKey;
        $this->imageHelper = $imageHelper;
        $this->registry = $registry;
        $this->priceHelper = $priceHelper;
        $this->request = $request;
        $this->productFactory = $productFactory;
    }

    /**
     * Load the current product data.
     */
    public function getData()
    {
        $output = [];        
        $product = $this->getProduct();
        if ($product) {
            $output = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'is_free' => $product->getFinalPrice() == 0,
                'price' => $this->getProductPrice(),
                'url' => $this->getProductImageUrl(),
                'form_key' => $this->getFormKey()
            ];
        }

        return $output;
    }

    /**
     * Get the current product.
     */
    public function getProduct()
    {
        $pid = $this->request->getParam('pid', 0);
        if ((int) $pid > 0) {
            return $this->productFactory->create()->load($pid);
        }
        else {
            return $this->registry->registry('current_product');
        }
    }

    /**
     * Check if the user is in a list view.
     */
    public function isListView()
    {
        $product = $this->registry->registry('current_product');
        $productExists = $product && $product->getId() > 0;
        return !$productExists;
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
    public function getProductPrice()
    {
        return $this->priceHelper->currency(
            $this->getProduct()->getFinalPrice(),
            true,
            false
        );
    }

    /**
     * Get the current product image url.
     */
    public function getProductImageUrl()
    {
        return $this->imageHelper->init(
            $this->getProduct(),
            'product_base_image'
        )->constrainOnly(FALSE)
        ->keepAspectRatio(TRUE)
        ->keepFrame(FALSE)
        ->getUrl();
    }
}
