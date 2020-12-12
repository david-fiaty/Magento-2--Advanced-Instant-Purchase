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
     * Class Customer helper constructor.
     */
    public function __construct(
        \Magento\Customer\Block\Account\AuthorizationLink $authLink,
        \Magento\Customer\Model\Address $addressModel,
        \Magento\Framework\Locale\Resolver $localeResolver,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroupCollection
    ) {
        $this->authLink = $authLink;
        $this->addressModel = $addressModel;
        $this->localeResolver = $localeResolver;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->customerGroupCollection = $customerGroupCollection;
    }

    /**
     * Get a customer.
     */
    public function getCustomer()
    {
        return $this->customerSession->getCustomer();
    }

    /**
     * Get a billing address.
     */
    public function getBillingAddress()
    {
        return $this->addressModel->load(
            $this->getCustomer()->getDefaultBilling()
        );
    }

    /**
     * Get a shipping address.
     */
    public function getShippingAddress()
    {
        return $this->addressModel->load(
            $this->getCustomer()->getDefaultShipping()
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
                'language' => $this->getUserLanguage()
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
     * Get the current customer group.
     */
    public function getCustomerGroupId()
    {
        return $this->customerSession->getCustomer()->getGroupId();
    }
}
