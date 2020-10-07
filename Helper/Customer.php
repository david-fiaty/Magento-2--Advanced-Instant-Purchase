<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

/**
 * Class Customer
 */
class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var AddressFactory
     */
    public $addressFactory;

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
     * Class Customer constructor.
     */
    public function __construct(
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Framework\Locale\Resolver $localeResolver,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->addressFactory = $addressFactory;
        $this->localeResolver = $localeResolver;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * Get a customer.
     */
    public function getCustomer()
    {
        // Load the customer instance
        $id = $this->customerSession->getCustomer()->getId();
        $customer = $this->customerFactory->create()->load($id);
        $customer->setWebsiteId(
            $this->storeManager->getStore()->getWebsiteId()
        );

        return $customer;
    }

    /**
     * Get a billing address.
     */
    public function getBillingAddress()
    {
        return $this->addressFactory->create()->load(
            $this->getCustomer->getDefaultBilling()
        );
    }

    /**
     * Get a shipping address.
     */
    public function getShippingAddress()
    {
        return $this->addressFactory->create()->load(
            $this->getCustomer->getDefaultShipping()
        );
    }

    /**
     * Get the customer addresses.
     */
    public function getAddresses()
    {
        $output = [];
        $addresses = $this->getCustomer()->getAddresses();
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

    /**
     * Get the current user status.
     */
    public function getUserParams()
    {
        return [
            'user' => [
                'connected' => $this->isLoggedIn(),
                'language' => $this->getUserLanguage()
            ]
        ];
    }
}
