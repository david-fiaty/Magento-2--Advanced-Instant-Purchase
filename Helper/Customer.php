<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

/**
 * Class Customer
 */
class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var toreManagerInterface
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
     * @var CustomerData
     */
    public $customerData;

    /**
     * Class Customer constructor.
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Naxero\AdvancedInstantPurchase\Model\InstantPurchase\CustomerData $customerData,
        \Naxero\AdvancedInstantPurchase\Model\InstantPurchase\ShippingSelector $shippingSelector
    ) {
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->customerData = $customerData;
        $this->shippingSelector = $shippingSelector;
    }

    /**
     * Get the current customer addresses.
     */
    public function getConfirmContent()
    {
        if ($this->customerSession->isLoggedIn()) {
            // Prepare the required parameters
            $customerId = $this->customerSession->getCustomer()->getId();
            $customer = $this->customerFactory->create();
            $websiteId = $this->storeManager->getStore()->getWebsiteId();
            $customer->setWebsiteId($websiteId);
            $customerModel = $customer->load($customerId);
    
            // Prepare the output arrays
            $customerAddressData = [];

            // Get the addresses list
            $addresses = $customerModel->getAddresses();
    
            // Prepare the addresses
            if (!empty($addresses)) {
                foreach ($addresses as $address) {
                    $addressArray = $address->toArray();
                    if ($addressArray['is_active'] == 1) {
                        $customerAddressData['addresses'][] = $addressArray;
                    }
                }
            }

            // Prepare the shipping methods
            $shippingMethods = $this->shippingSelector->getShippingMethods();
            if (!empty($shippingMethods)) {
                $customerAddressData['shippingMethods'] = $shippingMethods;
            }

            // Prepare the instant purchase data
            $customerData = $this->customerData->getSectionData();
            if (!empty($customerData)) {
                $customerAddressData['sectionData'] = $customerData;
            }

            return $customerAddressData;
        }

        return [];
    }
}
