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
     * @var CustomerData
     */
    public $customerData;

    /**
     * @var VaultHandlerService
     */
    public $vaultHandler;

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
        \Naxero\AdvancedInstantPurchase\Model\Service\VaultHandlerService $vaultHandler

    ) {
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->configHelper = $configHelper;
        $this->customerData = $customerData;
        $this->shippingSelector = $shippingSelector;
        $this->vaultHandler = $vaultHandler;
    }

    /**
     * Get the current customer addresses.
     */
    public function getConfirmContent()
    {
        if ($this->customerSession->isLoggedIn()) {
            $config = $this->configHelper->getValues();

            // Prepare the required parameters
            $customerId = $this->customerSession->getCustomer()->getId();
            $customer = $this->customerFactory->create();
            $websiteId = $this->storeManager->getStore()->getWebsiteId();
            $customer->setWebsiteId($websiteId);
            $customerModel = $customer->load($customerId);
    
            // Prepare the output arrays
            $confirmationData = [];

            // Get the addresses list
            $addresses = $customerModel->getAddresses();
            if (!empty($addresses)) {
                foreach ($addresses as $address) {
                    $addressArray = $address->toArray();
                    if ($addressArray['is_active'] == 1) {
                        $confirmationData['addresses'][] = $addressArray;
                    }
                }
            }

            // Get the popup title
            $confirmationData['popup'] = [
                'title' => $config['display']['popup_title'],
                'header_text' => $config['display']['popup_header_text'],
                'footer_text' => $config['display']['popup_footer_text']
            ];

            // Get the saved cards list
            $confirmationData['savedCards'] = $this->vaultHandler->getUserCards();

            // Prepare the shipping rates
            $confirmationData['shippingRates'] = $this->shippingSelector->getShippingRates($customer);

            // Prepare the instant purchase data
            $customerData = $this->customerData->getSectionData();
            if (!empty($customerData)) {
                $confirmationData['sectionData'] = $customerData;
            }

            return $confirmationData;
        }

        return [];
    }
}
