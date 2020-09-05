<?php
namespace Naxero\AdvancedInstantPurchase\Block;

/**
 * Configuration for JavaScript instant purchase button component.
 */
class Config extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Config
     */
    public $instantPurchaseConfig;

    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @var Session
     */
    public $assetRepo;

    /**
     * @var Config
     */
    public $configHelper;

    /**
     * @var Resolver
     */
    public $localeResolver;

    /**
     * Button class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\InstantPurchase\Model\Config $instantPurchaseConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        \Magento\Framework\Locale\Resolver $localeResolver,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->instantPurchaseConfig = $instantPurchaseConfig;
        $this->customerSession = $customerSession;
        $this->assetRepo = $assetRepo;
        $this->configHelper = $configHelper;
        $this->localeResolver = $localeResolver;
    }

    /**
     * Get the module config values.
     */
    public function getConfig()
    {
        $values = $this->configHelper->getValues();
        unset($values['card_form']);
        return $values;
    }

    /**
     * Get the loader icon URL.
     */
    public function getLoaderIconUrl()
    {
        return $this->assetRepo->getUrl('Naxero_AdvancedInstantPurchase::images/ajax-loader.gif');
    }

    /**
     * Get the user locale.
     */
    public function getUserLanguage()
    {
        return $this->localeResolver->getLocale();
    }

    /**
     * Checks if button enabled.
     *
     * @return bool
     * @since 100.2.0
     */
    public function isEnabled(): bool
    {
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
