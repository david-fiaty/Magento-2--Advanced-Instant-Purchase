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
     * PlaceOrderService constructor.
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Naxero\BuyNow\Helper\Customer $customerHelper,
        \Magento\Framework\HTTP\Client\Curl $curl
    ) {
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
        ->addQuoteItem();

        exit();

        return $order;
    }

    /**
     * Load the obect instance data.
     */
    public function loadData($params)
    {
        // Set the access token
        $this->accessToken = $this->customerHelper->getAccessToken(
            $this->customerHelper->getCustomer()->getId()
        );

        // Store data
        $this->store = $this->storeManager->getStore();

        // Product data
        $this->product = $this->productRepository->getById(
            $this->params['product'], false,
            $this->store->getId(), false
        );

        // Request parameters
        $this->params = $params;

        // Request headers
        $this->headers = [
            'Content-Type' => 'application/json',
            'Authorization' => $this->accessToken
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
        $response = $request->getBody();

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/3.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($response, 1));

        return $this;
    }

    /**
     * Add a product to the quote.
     */
    public function addQuoteItem()
    {
        return $this;
    }
}
