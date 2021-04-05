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
 * Class PlaceOrderService.
 */
class PlaceOrderService
{
    /**
     * @var array
     */
    public $removeAddressFields = [
        'entity_id',
        'increment_id',
        'parent_id',
        'created_at',
        'updated_at',
        'is_active',
        'vat_id',
        'vat_is_valid',
        'vat_request_date',
        'vat_request_id',
        'vat_request_success'
    ];

    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    public $productRepository;

    /**
     * @var Curl
     */
    public $curl;

    /**
     * @var Customer
     */
    public $customerHelper;

    /**
     * @var array
     */
    public $headers;

    /**
     * @var array
     */
    public $data = [];

    /**
     * PlaceOrderService constructor.
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Naxero\BuyNow\Helper\Customer $customerHelper,
        \Magento\Framework\HTTP\Client\Curl $curl
    ) {
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->customerHelper = $customerHelper;
        $this->curl = $curl;
    }

    /**
     * Place an order.
     */
    public function placeOrder($params)
    {
        $order = $this->loadData($params)
        ->createQuote()
        ->addProduct()
        ->prepareCheckout()
        ->createOrder();

        return $order;
    }

    /**
     * Load the obect instance data.
     */
    public function loadData($data)
    {
        // Request parameters
        $this->data['params'] = $data['nbn']['params'];

        // Set the access token
        $this->data['access_token'] = $this->customerHelper->getAccessToken(
            $this->customerSession->getId()
        );

        // Store data
        $this->data['store'] = $this->storeManager->getStore();

        // Request headers
        $this->headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->data['access_token']
        ];

        // Product data
        $this->data['product'] = $this->productRepository->getById(
            $this->data['params']['product_id'],
            false,
            $this->data['store']->getId(),
            false
        );

        // Billing address
        $this->data['billing_address'] = $this->prepareAddress(
            $this->data['params']['billing_address_id']
        );
        
        // Shipping address
        $this->data['shipping_address'] = $this->prepareAddress(
            $this->data['params']['shipping_address_id']
        );

        return $this;
    }

    /**
     * Create a quote.
     */
    public function createQuote()
    {
        // Send the request
        $response = $this->sendRequest('carts/mine'); 

        // Get the response
        $this->data['quote_id'] = (int) $response;

        return $this;
    }

    /**
     * Add a product to the quote.
     */
    public function addProduct()
    {
        // Prepare the URL
        // Todo - handle different product types
        // https://devdocs.magento.com/guides/v2.2/rest/tutorials/orders/order-add-items.html

        // Prepare the payload
        $payload = [
            'cartItem' => [
                'sku' => $this->data['product']->getSku(),
                'qty' => 1, // Todo - get qty from request
                'quote_id' => $this->data['quote_id']
            ]
        ];

        // Send the request
        $response = $this->sendRequest('carts/mine/items', $payload); 

        return $this;
    }

    /**
     * Prepare the order checkout.
     */
    public function prepareCheckout()
    {
        // Prepare the payload
        $payload = [
            'addressInformation' => [
                'shipping_address' => $this->data['shipping_address'],
                'billing_address' => $this->data['billing_address'],
                'shipping_carrier_code' => $this->data['params']['shipping_carrier_code'],
                'shipping_method_code' => $this->data['params']['shipping_method_code']
            ]
        ];

        // Send the request
        $response = $this->sendRequest('carts/mine/shipping-information', $payload); 

        return $this;
    }

    /**
     * Create the order.
     */
    public function createOrder()
    {
        // Prepare the payload
        $payload = [
            'paymentMethod' => [
                //'method' => $this->data['params']['payment_method_code']
                'method' => 'checkmo'
            ],
            'billing_address' => $this->data['billing_address']
        ];

        // Send the request
        $response = $this->sendRequest('carts/mine/payment-information', $payload); 

        return $order;
    }

    /**
     * Send a request.
     */
    public function sendRequest($endpoint, $payload = [])
    {
        // Prepare parameters
        $url = $this->getUrl($endpoint);
        $request = $this->curl;

        // Send the request
        $request->setHeaders($this->headers);
        $request->post($url, json_encode($payload));

        // Process the response
        $response = json_decode($request->getBody(), true);

        return $response;
    }

    /**
     * Get the endpoint URL.
     */
    public function getUrl($endpoint)
    {

        return 'https://enag0ei84vpte.x.pipedream.net/';

        return $this->data['store']->getBaseUrl()
        . 'rest/' . $this->data['store']->getCode() 
        . '/' . 'V1/' . $endpoint;
    }

    /**
     * Prepare an address for the request.
     */
    public function prepareAddress($addressId)
    {
        // Get the address data
        $data = $this->customerHelper->loadAddress($addressId)->getData();

        // Remove non relevant fields
        $data = array_diff_key($data, array_flip($this->removeAddressFields));
        
        // Update the address street field
        if (isset($data['street'])) {
            $data['street'] = [$data['street']];
        }

        return $data;
    }
}
