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

namespace Naxero\BuyNow\Model\Payment\Integration\Magento;

/**
 * Class Index.
 */
class Index implements \Naxero\BuyNow\Model\Payment\Integration\PaymentIntegrationInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    public $orderRepository;

    /**
     * @var PaymentConfiguration
     */
    public $paymentConfiguration;

    /**
     * @var Purchase
     */
    private $purchase;

    /**
     * @var string|array|object
     */
    public $response;

    /**
     * Index constructor.
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\InstantPurchase\Model\QuoteManagement\PaymentConfiguration $paymentConfiguration,
        \Magento\InstantPurchase\Model\QuoteManagement\Purchase $purchase
    ) {
        $this->orderRepository = $orderRepository;
        $this->paymentConfiguration = $paymentConfiguration;
        $this->purchase = $purchase;
    }

    /**
     * Send a payment request.
     */
    public function sendRequest($quote, $paymentData)
    {
        // Get the payment response
        $response = '';

        // Assign the response to self
        $this->response = $response;

        return $this;
    }

    /**
     * Chcek if a payment request is successful.
     */
    public function paymentSuccess()
    {
        return true;
    }
    
    /**
     * Create a new order.
     */
    public function createOrder($quote, $paymentResponse)
    {
        // Configure the payment
        $quote = $this->paymentConfiguration->configurePayment(
            $quote,
            $instantPurchaseOption->getPaymentToken()
        );

        // Place the order with a core payment method
        $orderId = $this->purchase->purchase($quote);
        $order = $this->orderRepository->get($orderId);

        return $order;
    }
}
