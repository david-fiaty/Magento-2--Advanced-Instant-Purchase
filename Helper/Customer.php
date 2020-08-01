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
     * Class Customer constructor.
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
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
    
            // Prepare the output
            if (!empty($addresses)) {
                foreach ($addresses as $address) {
                    $addressArray = $address->toArray();
                    if ($addressArray['is_active'] == 1) {
                        $customerAddressData[] = $addressArray;
                    }
                }
            }

            return $customerAddressData;
        }

        return [];
    }
}
