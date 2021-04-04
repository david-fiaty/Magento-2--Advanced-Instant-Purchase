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
        ->addQuoteItem()
        ->prepareCheckout();

        exit();

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

        // Product data
        $this->data['product'] = $this->productRepository->getById(
            $this->data['product'],
            false,
            $this->data['store']->getId(),
            false
        );

        // Request headers
        $this->headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->data['access_token']
        ];

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
        $this->data['quote_id'] = (int) $request->getBody();

        return $this;
    }

    /**
     * Add a product to the quote.
     */
    public function addQuoteItem()
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

        // Send the request
        $request = $this->curl;
        $request->setHeaders($this->headers);
        $request->post($url, $payload);

        // Get the response for error handling
        // $response = $request->getBody();

        return $this;
    }

    /**
     * Prepare the order checkout.
     */
    public function prepareCheckout()
    {
        // Set billing and shipping information
        //<host>/rest/<store_code>/V1/carts/mine/shipping-information


        return $this;

    }

    /**
     * Create the order.
     */
    public function createOrder()
    {
        //<host>/rest/<store_code>/V1/carts/mine/payment-information
        // payload
        /*
        {
            "paymentMethod": {
                        "method": "banktransfer"
            },
            "billing_address": {
                        "email": "jdoe@example.com",
                    "region": "New York",
                    "region_id": 43,
                    "region_code": "NY",
                        "country_id": "US",
                        "street": ["123 Oak Ave"],
                        "postcode": "10577",
                        "city": "Purchase",
                        "telephone": "512-555-1111",
                        "firstname": "Jane",
                        "lastname": "Doe"
            }
        }       
    */

        //return $order;
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
}
