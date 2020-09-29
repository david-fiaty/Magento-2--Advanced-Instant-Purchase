<?php
namespace Naxero\AdvancedInstantPurchase\Block\Button;

/**
 * Configuration for JavaScript instant purchase button component.
 */
class ListButton extends \Magento\Catalog\Block\Product\ProductList\Item\Block
{
    /**
     * @var Config
     */
    public $configHelper;

    /**
     * @var Customer
     */
    public $customerHelper;

    /**
     * @var Product
     */
    public $productHelper;

    /**
     * Button class constructor.
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Product $productHelper,
        array $data = []
    ) {
        $this->configHelper = $configHelper;
        $this->customerHelper = $customerHelper;
        $this->productHelper = $productHelper;

        parent::__construct($context, $data);
    }

    /**
     * Checks if the button should be didsplayed.
     */
    public function shouldDisplay()
    {
        $config = $this->configHelper->getValues();

        return $this->configHelper->bypassLogin()
        && $this->configHelper->isEnabled()
        && $config['display']['product_view']
        && $this->productHelper->isListView();
    }
}
