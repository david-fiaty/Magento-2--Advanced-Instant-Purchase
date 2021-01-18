<?php

/**
 * Naxero.com
 * Professional ecommerce integrations for Magento.
 *
 * PHP version 7
 *
 * @category  Magento2
 * @package   Naxero
 * @author    Platforms Development Team <contact@naxero.com>
 * @copyright © Naxero.com all rights reserved
 * @license   https://opensource.org/licenses/mit-license.html MIT License
 * @link      https://www.naxero.com
 */

namespace Naxero\BuyNow\Helper;

/**
 * Class Customer helper.
 */
class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var AddressRepositoryInterface
     */
    public $addressRepository;

    /**
     * @var Customer
     */
    public $customerModel;

    /**
     * @var AuthorizationLink
     */
    public $authLink;

    /**
     * @var Address
     */
    public $addressModel;

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
     * @var Collection
     */
    public $customerGroupCollection;

    /**
     * @var Config
     */
    public $configHelper;

    /**
     * Class Customer helper constructor.
     */
    public function __construct(
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Customer\Block\Account\AuthorizationLink $authLink,
        \Magento\Customer\Model\Address $addressModel,
        \Magento\Framework\Locale\Resolver $localeResolver,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroupCollection,
        \Naxero\BuyNow\Helper\Config $configHelper
    ) {
        $this->addressRepository = $addressRepository;
        $this->customerModel = $customerModel;
        $this->authLink = $authLink;
        $this->addressModel = $addressModel;
        $this->localeResolver = $localeResolver;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->customerGroupCollection = $customerGroupCollection;
        $this->configHelper = $configHelper;
    }

    /**
     * Get a customer by id.
     */
    public function getCustomer($customerId = null)
    {
        $customerId = $customerId ? $customerId : $this->customerSession->getCustomer()->getId();

        if ((int) $customerId > 0) {
            return $this->customerModel->load($customerId);
        }

        return null;
    }

    /**
     * Get a billing address.
     */
    public function getBillingAddress($customerId = null)
    {
        return $this->addressModel->load(
            $this->getCustomer($customerId)->getDefaultBilling()
        );
    }

    /**
     * Get a shipping address.
     */
    public function getShippingAddress($customerId = null)
    {
        return $this->addressModel->load(
            $this->getCustomer($customerId)->getDefaultShipping()
        );
    }

    /**
     * Get the customer addresses.
     */
    public function getAddresses($customerId = null)
    {
        $output = [];
        $addresses = $this->getCustomer($customerId)->getAddresses();
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
     * @param $addressId
     *
     * @return \Magento\Customer\Api\Data\AddressInterface
     */
    public function getAddressData($addressId)
    {
        try {
            $addressData = $this->addressRepository->getById($addressId);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
        
        return $addressData;
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
        return $this->authLink->isLoggedIn();
    }

    /**
     * Get the current user status.
     */
    public function getUserParams()
    {
        return [
            'user' => [
                'connected' => $this->isLoggedIn(),
                'language' => $this->getUserLanguage(),
            ]
        ];
    }

    /**
     * Get the available customer groups.
     */
    public function getCustomerGroups()
    {
        return $this->customerGroupCollection->toOptionArray();
    }

    /**
     * Check if the customer group is valid for button display.
     */
    public function canDisplayForGroup($config)
    {
        // Prepare the parameters
        $cutomerGroupId = $this->customerSession->getCustomer()->getGroupId();
        $customerGroups = explode(',', $config['buttons']['customer_groups']);
        $noGroupFound = empty($customerGroups) || (isset($customerGroups[0]) && empty($customerGroups[0]));

        return $noGroupFound || in_array($cutomerGroupId, $customerGroups);
    }
}
