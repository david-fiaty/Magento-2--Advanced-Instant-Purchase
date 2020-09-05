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
     * @var Repository
     */
    public $assetRepo;

    /**
     * @var Registry
     */
    public $registry;  

    /**
     * @var Resolver
     */
    public $localeResolver;

    /**
     * @var Image
     */
    public $imageHelper;

    /**
     * @var Data
     */
    public $priceHelper;

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
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Locale\Resolver $localeResolver,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->instantPurchaseConfig = $instantPurchaseConfig;
        $this->customerSession = $customerSession;
        $this->assetRepo = $assetRepo;
        $this->registry = $registry;
        $this->localeResolver = $localeResolver;
        $this->imageHelper = $imageHelper;
        $this->priceHelper = $priceHelper;
        $this->configHelper = $configHelper;

    }

    /**
     * Get the module config values.
     */
    public function getConfig()
    {
        // Get the module config
        $aiiConfig = $this->configHelper->getValues();

        // Filter parameters
        unset($aiiConfig['card_form']);

        // Loader icon
        $aiiConfig['ui']['loader'] = $this->getLoaderIconUrl();

        // Product instance
        $product = $this->getProduct();
        $aiiConfig['product'] = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getProductPrice(),           
            'url' => $this->getProductImageUrl()
        ];

        // User info
        $aiiConfig['user'] = [
            'loggedIn' => $this->customerSession->isLoggedIn(),
            'language' => $this->getUserLanguage()
        ];

        return json_encode($aiiConfig);
    }

    /**
     * Get the current product.
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
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
