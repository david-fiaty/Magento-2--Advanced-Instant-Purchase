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
     * @var OrderRepositoryInterface
     */
    public $orderRepository;

    /**
     * @var Curl
     */
    public $curl;

    /**
     * @var Customer
     */
    public $customerHelper;

    /**
     * @var ApiHandlerService
     */
    public $apiHandlerService;

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
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Naxero\BuyNow\Helper\Customer $customerHelper,
        \Naxero\BuyNow\Model\Service\ApiHandlerService $apiHandlerService
    ) {
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
        $this->curl = $curl;
        $this->customerHelper = $customerHelper;
        $this->apiHandlerService = $apiHandlerService;
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

        // Store data
        $this->data['store'] = $this->storeManager->getStore();

        // Request headers
        $this->data['headers'] = $this->apiHandlerService->getHeaders();

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
        $url = $this->apiHandlerService->getCreateQuoteUrl();
        $quoteId = (int) $this->sendRequest($url); 

        // Get the response
        $this->data['quote_id'] = (int) $quoteId;

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

        // Get the request URL
        $url = $this->apiHandlerService->getAddProductUrl();
        $url = str_replace('<cartId>', $this->data['quote_id']);
        $response = $this->sendRequest($url, $payload); 

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
        $url = $this->apiHandlerService->getPrepareCheckoutUrl();
        $url = str_replace('<cartId>', $this->data['quote_id']);
        $response = $this->sendRequest($url, $payload); 

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
                'method' => $this->data['params']['payment_method_code']
            ],
            'billing_address' => $this->data['billing_address']
        ];

        // Send the request
        $url = $this->apiHandlerService->getCreateOrderUrl();
        $response = $this->sendRequest($url, $payload);
        $orderId = (int) $response;

        // Check the order
        if ($orderId  > 0) {
            return $this->orderRepository->get($orderId);
        } 
        else {
            try {
                $response = json_decode($response);
                if (isset($response['message'])) {
                    $response = $response['message'];
                }
            }
            catch (\Exception $e) {
                $response = $e->getMessage();
            }

            // Error message handling
            throw new \Magento\Framework\Exception\LocalizedException(
                __($response)
            );
        }

        return null;
    }

    /**
     * Send a request.
     */
    public function sendRequest($url, $payload = [])
    {
        // Send the request
        $request = $this->curl;
        $request->setHeaders($this->data['headers']);
        $request->setOption(CURLOPT_RETURNTRANSFER, true);
        $request->setOption(CURLOPT_POSTFIELDS, json_encode($payload));
        $request->post($url, []);

        // Process the response
        $response = json_decode($request->getBody(), true);

        return $response;
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
