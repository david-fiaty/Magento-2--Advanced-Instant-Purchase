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
 * Class Order helper.
 */
class Order extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var Coupon
     */
    public $couponModel;

    /**
     * @var RuleRepositoryInterface
     */
    public $ruleRepository;

    /**
     * @var Curl
     */
    public $curl;

    /**
     * Class Order helper constructor.
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\SalesRule\Model\Coupon $couponModel,
        \Magento\SalesRule\Api\RuleRepositoryInterface $ruleRepository,
        \Magento\Framework\HTTP\Client\Curl $curl
    ) {
        $this->storeManager = $storeManager;
        $this->couponModel = $couponModel;
        $this->ruleRepository = $ruleRepository;
        $this->curl = $curl;
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

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/2.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($response, 1));

    }

    /**
     * Apply a discount code to an amount.
     */
    public function applyDiscount($rule, $amount)
    {
        $discountedAmount = $amount - (float) $rule->getDiscountAmount();
        return $discountedAmount > 0 ? $discountedAmount : 0;
    }

    /**
     * Get a coupon code data.
     */
    public function getCouponRule($couponCode)
    {
        $ruleId =  $this->couponModel->loadByCode($couponCode)->getRuleId();
        if ((int) $ruleId > 0) {
            return $this->ruleRepository->getById($ruleId);
        }

        return null;
    }
}
