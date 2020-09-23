<?php
namespace Naxero\AdvancedInstantPurchase\Block;

/**
 * Configuration for JavaScript instant purchase button component.
 */
class Button extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Config
     */
    public $instantPurchaseConfig;

    /**
     * @var Config
     */
    public $configHelper;

    /**
     * Button class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\InstantPurchase\Model\Config $instantPurchaseConfig,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->instantPurchaseConfig = $instantPurchaseConfig;
        $this->configHelper = $configHelper;
    }

    /**
     * Get the module config values.
     */
    public function getConfig()
    {
        return $this->configHelper->getValues();
    }

    /**
     * Checks if button enabled.
     *
     * @return bool
     * @since 100.2.0
     */
    public function isEnabled(): bool
    {
        // todo - check validation rules
        return true;
        return $this->instantPurchaseConfig->isModuleEnabled($this->getCurrentStoreId())
        || $this->getConfig()['guest']['show_guest_button'] == 1;
    }

    /**
     * @inheritdoc
     * @since 100.2.0
     */
    public function getJsLayout(): string
    {
        $buttonText = $this->instantPurchaseConfig->getButtonText($this->getCurrentStoreId());
        $purchaseUrl = $this->getUrl('instantpurchase/button/placeOrder', ['_secure' => true]);
        
        // String data does not require escaping here and handled on transport level and on client side
        $this->jsLayout['components']['instant-purchase']['config']['buttonText'] = $buttonText;
        $this->jsLayout['components']['instant-purchase']['config']['purchaseUrl'] = $purchaseUrl;
        return parent::getJsLayout();
    }

    /**
     * Returns active store view identifier.
     *
     * @return int
     */
    public function getCurrentStoreId(): int
    {
        return $this->_storeManager->getStore()->getId();
    }
}
