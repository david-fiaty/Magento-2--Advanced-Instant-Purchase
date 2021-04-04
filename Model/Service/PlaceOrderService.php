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
    public function loadData($params)
    {
        // Request parameters
        $this->data['params'] = $params;

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
            $params['product'],
            false,
            $this->data['store']->getId(),
            false
        );

        // Billing address
        $this->data['billing_address'] = $this->prepareAddress(
            $this->data['params']['nbn-billing-address-select']
        );
        
        // Shipping address
        $this->data['shipping_address'] = $this->prepareAddress(
            $this->data['params']['nbn-shipping-address-select']
        );

        return $this;
    }

    /**
     * Create a quote.
     */
    public function createQuote()
    {
        // Request URL
        $url = $this->getUrl('carts/mine');

        // Send the request
        $request = $this->curl;
        $request->setHeaders($this->headers);
        $request->post($url, []);

        // Get the response
        // $response = json_decode($request->getBody(), true);
        // $response->getStatus()
        $this->data['quote_id'] = (int) $request->getBody();

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
        $url = $this->getUrl('carts/mine/items');

        // Prepare the payload
        $payload = [
            'cartItem' => [
                'sku' => $this->data['product']->getSku(),
                'qty' => 1, // Todo - get qty from request
                'quote_id' => $this->data['quote_id']
            ]
        ];

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/a1.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(json_encode($payload));

        // Send the request
        $request = $this->curl;
        $request->setHeaders($this->headers);
        $request->post($url, $payload);

        return $this;
    }

    /**
     * Prepare the order checkout.
     */
    public function prepareCheckout()
    {
        // Get the request URL
        $url = $this->getUrl('carts/mine/shipping-information');

        // Prepare the payload
        $payload = [
            'addressInformation' => [
                'shipping_address' => $this->data['shipping_address'],
                'billing_address' => $this->data['billing_address']
            ],
            'shipping_carrier_code' => $this->data['params']['nbn-shipping-method-select'],
            'shipping_method_code' => $this->data['params']['nbn-shipping-method-select']
        ];

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/a2.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(json_encode($payload));

        // Send the request
        $request = $this->curl;
        $request->setHeaders($this->headers);
        $request->post($url, $payload);
        
        return $this;
    }

    /**
     * Create the order.
     */
    public function createOrder()
    {
        // Get the request URL
        $url = $this->getUrl('carts/mine/payment-information');

        // Prepare the payload
        $payload = [
            'paymentMethod' => [
                'method' => $this->data['params']['nbn-payment-method-select']
            ],
            'billing_address' => $this->data['billing_address']
        ];

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/a3.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(json_encode($payload));

        // Send the request
        $request = $this->curl;
        $request->setHeaders($this->headers);
        $request->post($url, $payload);

        return $order;
    }

    /**
     * Get the endpoint URL.
     */
    public function getUrl($endpoint)
    {
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

        // Update the address street field
        if (isset($data['street'])) {
            $data['street'] = [$data['street']];
        }

        // Remove non relevant fields
        $data = array_diff($data, $this->removeAddressFields);

        return $data;
    }
}
