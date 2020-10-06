<?php
namespace Naxero\AdvancedInstantPurchase\Block\Confirmation;

/**
 * Data Class.
 */
class Data extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Purchase
     */
    public $purchaseHelper;

    /**
     * @var Config
     */
    public $configHelper;

    /**
     * Data constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Naxero\AdvancedInstantPurchase\Helper\Purchase $purchaseHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        array $data = []
    ) {
        $this->purchaseHelper = $purchaseHelper;
        $this->configHelper = $configHelper;

        parent::__construct($context, $data);
    }

    /**
     * Get the connfirmation popup content.
     */
    public function getConfirmContent() {
        return $this->purchaseHelper->getConfirmContent();
    }

    /**
     * Get the module config.
     */
    public function getConfig() {
        return $this->configHelper->getFrontendValues();
    }
}
