<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

/**
 * Class Product helper.
 */
class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Configurable
     */
    public $productTypeConfigurable;

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
     * @var Config
     */
    public $configHelper;

    /**
     * @var Tools
     */
    public $toolsHelper;

    /**
     * Class Product helper constructor.
     */
    public function __construct(
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $productTypeConfigurable,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Tools $toolsHelper
    ) {
        $this->productTypeConfigurable = $productTypeConfigurable;
        $this->imageHelper = $imageHelper;
        $this->priceHelper = $priceHelper;
        $this->request = $request;
        $this->productFactory = $productFactory;
        $this->stockItemRepository = $stockItemRepository;
        $this->configHelper = $configHelper;
        $this->toolsHelper = $toolsHelper;
    }

    /**
     * Load the current product data.
     */
    public function getData($productId)
    {
        $output = [];
        $product = $this->getProduct($productId);
        if ($product) {
            // Prepare the base data
            $output = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $this->getProductPrice($productId),
                'is_free' => $this->isFree($productId),
                'form_key' => $this->toolsHelper->getFormKey(),
                'in_stock' => $this->isInStock($productId),
                'has_options' => (bool) $this->hasOptions($productId),
                'button_id' => $this->getButtonId($productId),
                'button_selector' => '#' . $this->getButtonId($productId),
                'image_url' => $this->getProductImageUrl($productId),
                'page_url' => $product->getProductUrl(),
                'options' => $this->buildOptionsArray($productId)
            ];
        }

        return $output;
    }

    /**
     * Get a block button id.
     */
    public function getButtonId($productId)
    {
        return 'aip-button-' . $productId;
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
     * Get a product options.
     */
    public function getOptions($productId)
    {
        return $this->productTypeConfigurable->getConfigurableAttributesAsArray(
            $this->getProduct($productId)
        );
    }

    /**
     * Format a product options.
     */
    public function buildOptionsArray($productId)
    {
        // Prepare the output
        $output = [];

        // Get the product options
        if ($this->hasOptions($productId)) {
            $data = $this->getOptions($productId);
            foreach ($data as $key => $row) {
                //$output[$key] = $row;
                //var_dump($row);

                /*
                var_dump('<h1>Options</h1>');
                var_dump($row['options']);

                var_dump('<h1>Values</h1>');
                var_dump($row['values']);

                */


                if (isset($row['options']) && is_array($row['options']) && !empty($row['options'])) {
                    foreach ($row['options'] as $option) {

                    }
                } 
            }
        }

        return $output;
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
        )->constrainOnly(false)
        ->keepAspectRatio(true)
        ->keepFrame(false)
        ->getUrl();
    }

    /**
     * Check if a product exists.
     */
    public function isProduct($productId)
    {
        $product = $this->getProduct($productId);

        return $product && (int) $product->getId() > 0;
    }
}
