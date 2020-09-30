<?php
namespace Naxero\AdvancedInstantPurchase\Block\Button;

/**
 * Configuration for JavaScript instant purchase button component.
 */
class ViewButton extends \Magento\Framework\View\Element\Template
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
        \Magento\Framework\View\Element\Template\Context $context,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        //\Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Product $productHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configHelper = $configHelper;
        //$this->customerHelper = $customerHelper;
        $this->productHelper = $productHelper;
    }

    /**
     * Get the current product.
     */
    public function getProduct()
    {
        return $this->productHelper->getProduct();
    }

    /**
     * Get the block config.
     */
    public function getConfig()
    {
        $config = $this->configHelper->getValues();
        $condition = $config['guest']['show_guest_button']
        && $config['general']['enabled']
        && $config['display']['product_view']
        && !$this->productHelper->isListView();

        return $condition ? $config : null;
    }
}