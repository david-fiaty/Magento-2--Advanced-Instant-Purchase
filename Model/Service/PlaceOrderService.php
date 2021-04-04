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
    public $params;

    /**
     * @var array
     */
    public $headers;

    /**
     * @var string
     */
    public $accessToken;

    /**
     * @var int
     */
    public $quoteId;

    /**
     * @var Object
     */
    public $product;

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
        $this->params = $params;

        // Set the access token
        $this->accessToken = $this->customerHelper->getAccessToken(
            $this->customerSession->getId()
        );

        // Store data
        $this->store = $this->storeManager->getStore();

        // Product data
        $this->product = $this->productRepository->getById(
            $this->params['product'], false,
            $this->store->getId(), false
        );

        // Request headers
        $this->headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->accessToken
        ];

        return $this;
    }

    /**
     * Create a quote.
     */
    public function createQuote()
    {
        // Request URL
        $url = $this->store->getBaseUrl()
        . 'rest/' . $this->store->getCode() 
        . '/V1/carts/mine';

        // Send the request
        $request = $this->curl;
        $request->setHeaders($this->headers);
        $request->post($url, []);

        // Get the response
        $this->quoteId = (int) $request->getBody();

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
        $url = $this->store->getBaseUrl()
        . 'rest/' . $this->store->getCode() 
        . '/V1/carts/mine/items';

        // Prepare the payload
        $payload = [
            'cartItem' => [
                'sku' => $this->product->getSku(),
                'qty' => 1, // Todo - get qty from request
                'quote_id' => $this->quoteId
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
}
