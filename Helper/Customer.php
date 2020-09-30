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
     * @var Purchase
     */
    public $purchasetHelper;

    /**
     * @var VaultHandlerService
     */
    public $vaultHandler;

    /**
     * @var Object
     */
    public $customer;

    /**
     * @var Object
     */
    public $customerModel;

    /**
     * @var Array
     */
    public $config;

    /**
     * Class Customer constructor.
     */
    public function __construct(
        \Magento\Framework\Locale\Resolver $localeResolver,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        \Naxero\AdvancedInstantPurchase\Model\InstantPurchase\ShippingSelector $shippingSelector,
        \Naxero\AdvancedInstantPurchase\Model\Service\VaultHandlerService $vaultHandler,
        \Naxero\AdvancedInstantPurchase\Helper\Product $productHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Purchase $purchasetHelper

    ) {
        $this->localeResolver = $localeResolver;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->configHelper = $configHelper;
        $this->productHelper = $productHelper;
        $this->purchasetHelper = $purchasetHelper;
        $this->shippingSelector = $shippingSelector;
        $this->vaultHandler = $vaultHandler;
    }

    /**
     * Get the confirmation modal content.
     */
    public function getConfirmContent()
    {
        // Prepare the output array
        $confirmationData = [];
        $confirmationData['popup'] = $this->purchaseHelper->getPopupData();
        $confirmationData['product'] = $this->productHelper->getData();
        $confirmationData['addresses'] = [];
        $confirmationData['savedCards'] = [];
        $confirmationData['shippingRates'] = [];

        // Build the confirmation data
        if ($this->customerSession->isLoggedIn()) {
            // Load the customer data
            $this->loadCustomerData();

            // Confirmation data
            $confirmationData['addresses'] = $this->getAddresses();
            $confirmationData['savedCards'] = $this->vaultHandler->getUserCards();
            $confirmationData['shippingRates'] = $this->shippingSelector->getShippingRates(
                $this->customer
            );

            // Instant purchase data
            $customerSectionData = $this->customerData->getSectionData();
            if (!empty($customerSectionData)) {
                $confirmationData['sectionData'] = $customerSectionData;
            }
        }

        return $confirmationData;
    }

    /**
     * Load the customer data.
     */
    public function loadCustomerData()
    {
        // Load the customer instance
        $this->customer = $this->customerFactory->create();
        $this->customer->setWebsiteId(
            $this->storeManager->getStore()->getWebsiteId()
        );

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
