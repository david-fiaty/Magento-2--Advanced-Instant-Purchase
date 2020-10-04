<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

/**
 * Class Customer
 */
class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Resolver
     */
    public $localeResolver;

    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var CustomerFactory
     */
    public $customerFactory;

    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @var Config
     */
    public $configHelper;

    /**
     * @var Product
     */
    public $productHelper;

    /**
     * @var Object
     */
    public $customer;

    /**
     * @var Object
     */
    public $customerModel;

    /**
     * Class Customer constructor.
     */
    public function __construct(
        \Magento\Framework\Locale\Resolver $localeResolver,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        \Naxero\AdvancedInstantPurchase\Model\Service\VaultHandlerService $vaultHandler,
        \Naxero\AdvancedInstantPurchase\Helper\Product $productHelper

    ) {
        $this->localeResolver = $localeResolver;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->configHelper = $configHelper;
        $this->productHelper = $productHelper;
        $this->vaultHandler = $vaultHandler;
    }

    /**
     * Load a customer instance.
     */
    public function loadCustomer()
    {
        // Load the customer instance
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId(
            $this->storeManager->getStore()->getWebsiteId()
        );

        return $customer;
    }

    /**
     * Load the customer data.
     */
    public function loadCustomerData()
    {
        // Load the customer instance
        $this->customer = $this->load();

        // Load the customer model instance
        $this->customerModel = $this->customer->load(
            $this->customerSession->getCustomer()->getId()
        );
    }

    /**
     * Get the customer addresses.
     */
    public function getAddresses()
    {
        $output = [];
        $addresses = $this->customerModel->getAddresses();
        if (!empty($addresses)) {
            foreach ($addresses as $address) {
                $addressArray = $address->toArray();
                if ($addressArray['is_active'] == 1) {
                    $output[] = $addressArray;
                }
            }
        }

        return $output;
    }

    /**
     * Get the user locale.
     */
    public function getUserLanguage()
    {
        return $this->localeResolver->getLocale();
    }

    /**
     * Check if the cusomer is logged in.
     */
    public function isLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }
}
