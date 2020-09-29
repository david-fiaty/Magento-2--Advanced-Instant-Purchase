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


    public function test() {
        return 'test';
    }
    /**
     * Checks if the button should be didsplayed.
     */
    public function shouldDisplay()
    {
        return 'fat pussy';
        return true;


        $config = $this->configHelper->getValues();

        return $this->configHelper->bypassLogin()
        && $this->configHelper->isEnabled()
        && $config['display']['product_view']
        && !$this->productHelper->isListView();
    }
}