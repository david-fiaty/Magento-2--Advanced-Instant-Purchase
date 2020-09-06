<?php
namespace Naxero\AdvancedInstantPurchase\Block\Confirmation;

/**
 * Display Class.
 */
class Display extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Customer
     */
    public $customerHelper;

    /**
     * @var Config
     */
    public $configHelper;

    /**
     * Display constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerHelper = $customerHelper;
        $this->configHelper = $configHelper;
    }

    /**
     * Get the connfirmation popup content.
     */
    public function getConfirmContent() {
        return $this->customerHelper->getConfirmContent();
    }

    /**
     * Get the module config.
     */
    public function getConfig() {
        return $this->configHelper->getValues();
    }

}
