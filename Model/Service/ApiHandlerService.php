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

namespace Naxero\BuyNow\Model\Service;

/**
 * Class REST API ApiHandlerService.
 */
class ApiHandlerService
{
    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @var Customer
     */
    public $customerHelper;

    /**
     * ApiHandlerService constructor.
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Naxero\BuyNow\Helper\Customer $customerHelper
    ) {
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->customerHelper = $customerHelper;
    }
    
    /**
     * Get a request URL.
     */
    public function getUrl($action)
    {
        $action = explode('-', $action);
        $fn = 'get' . ucfirst($action[0]) . ucfirst($action[1]) . 'Url';

        return $this->$fn();
    }

    /**
     * Get the request headers.
     */
    public function getHeaders()
    {
        // Request headers
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        // Bearer token
        if (!$this->isGuest()) {
            $headers['Authorization'] = 'Bearer ' . $this->getAccessToken();
        }

        return $headers;
    }

    /**
     * Get the create quote URL.
     */
    public function getCreateQuoteUrl()
    {
        $url = $this->isGuest()
        ? '/guest-carts' :
        '/carts/mine';

        return $this->getRequestUrl($url);
    }

    /**
     * Get the add product URL.
     */
    public function getAddProductUrl()
    {
        $url = $this->isGuest()
        ? '/guest-carts/<cartId>/items'
        : '/carts/mine/items';

        return $this->getRequestUrl($url);
    }

    /**
     * Get the prepare checkout URL.
     */
    public function getPrepareCheckoutUrl()
    {
        $url = $this->isGuest()
        ? '/guest-carts/<cartId>/shipping-information'
        : '/carts/mine/shipping-information';

        return $this->getRequestUrl($url);
    }

    /**
     * Get the create order URL.
     */
    public function getCreateOrderUrl()
    {
        $url = $this->isGuest()
        ? '/guest-carts/<cartId>/payment-information'
        : 'carts/mine/payment-information';

        return $this->getRequestUrl($url);
    }
    
    /**
     * Get the endpoint URL.
     */
    public function getRequestUrl($endpoint)
    {
        // Get the store
        $store = $this->storeManager->getStore();

        // Build the URL
        $url = $store->getBaseUrl() . 'rest/' . $store->getCode()
        . '/' . 'V1/' . $endpoint;

        return $url;
    }

    /**
     * Check if the user is guest.
     */
    public function isGuest()
    {
        return !$this->customerSession->isLoggedIn();
    }

    /**
     * Get the access token.
     */
    public function getAccessToken()
    {
        return $this->customerHelper->getAccessToken(
            $this->customerSession->getId()
        );
    }
}
