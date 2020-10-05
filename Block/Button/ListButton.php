<?php
namespace Naxero\AdvancedInstantPurchase\Block\Button;

/**
 * ListButton class constructor.
 */
class ListButton extends \Magento\Framework\View\Element\Template
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
     * @var Purchase
     */
    public $purchaseHelper;

    /**
     * @var Product
     */
    public $productHelper;

    /**
     * ViewButton class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Purchase $purchaseHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Product $productHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        
        $this->configHelper = $configHelper;
        //$this->purchaseHelper = $purchaseHelper;
        $this->customerHelper = $customerHelper;
        $this->productHelper = $productHelper;
    }

    /**
     * Get the block config.
     */
    public function getConfig()
    {
        $config = $this->configHelper->getValues();
        $condition = $config['guest']['show_guest_button']
        && $config['general']['enabled']
        && $config['display']['product_list']
        && $this->productHelper->isListView();

        return $condition ? $config : null;
    }
}