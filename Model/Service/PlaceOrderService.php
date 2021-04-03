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
     * @var Curl
     */
    public $curl;

    /**
     * @var Customer
     */
    public $customerHelper;

    /**
     * PlaceOrderService constructor.
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Naxero\BuyNow\Helper\Customer $customerHelper
    ) {
        $this->storeManager = $storeManager;
        $this->customerHelper = $customerHelper;
    }

    /**
     * Place an order.
     */
    public function placeOrder($productId)
    {
        $this->createQuote();

    }

    /**
     * Create a quote.
     */
    public function createQuote()
    {
        $token = $this->customerHelper->getAccessToken(1);
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/2.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($token, 1));

        exit();

        // Prepare the needed parameters
        $store = $this->storeManager->getStore();
        $storeCode = $store->getCode();
        $data = [];

        // Prepare the request URL
        $baseUrl = $store->getBaseUrl();
        $url = $baseUrl . 'rest/' . $storeCode . '/V1/carts/mine';

        // Send the request
        $this->curl->setOption(CURLOPT_POSTFIELDS, $data);
        $this->curl->post($url, []);

        // Get the response
        $response = $this->curl->getBody();

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/3.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($response, 1));

    }
}
