<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

/**
 * Class Product
 */
class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
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
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->registry = $registry;
        $this->imageHelper = $imageHelper;
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
                'price' => $this->getProductPrice(),
                'url' => $this->getProductImageUrl()
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
