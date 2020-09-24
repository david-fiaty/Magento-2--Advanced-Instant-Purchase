<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

/**
 * Class Customer
 */
class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{
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
     * @var ConfigHelper
     */
    public $configHelper;

    /**
     * @var ProductHelper
     */
    public $productHelper;

    /**
     * @var CustomerData
     */
    public $customerData;

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
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        \Naxero\AdvancedInstantPurchase\Model\InstantPurchase\CustomerData $customerData,
        \Naxero\AdvancedInstantPurchase\Model\InstantPurchase\ShippingSelector $shippingSelector,
        \Naxero\AdvancedInstantPurchase\Model\Service\VaultHandlerService $vaultHandler,
        \Naxero\AdvancedInstantPurchase\Helper\Product $productHelper

    ) {
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->configHelper = $configHelper;
        $this->productHelper = $productHelper;
        $this->customerData = $customerData;
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
        $confirmationData['popup'] = $this->getPopupData();
        $confirmationData['product'] = $this->productHelper->getData();
        $confirmationData['addresses'] = [];
        $confirmationData['savedCards'] = [];
        $confirmationData['shippingRates'] = [];

        // Build the confirmation data
        if ($this->customerSession->isLoggedIn()) {
            // Get the config values
            $this->loadConfigData();

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
     * Load the config data.
     */
    public function loadConfigData()
    {
        $this->config = $this->configHelper->getValues();
    }

    /**
     * Get the popup data.
     */
    public function getPopupData()
    {
        return [
            'title' => $this->config['display']['popup_title'],
            'header_text' => $this->config['display']['popup_header_text'],
            'footer_text' => $this->config['display']['popup_footer_text']
        ];
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
}
