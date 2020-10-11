<?php
namespace Naxero\AdvancedInstantPurchase\Model\Payment\Integration\Magento;

/**
 * Class Index.
 */
class Index implements \Naxero\AdvancedInstantPurchase\Model\Payment\Integration\PaymentMethodInterface
{
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
        \Magento\InstantPurchase\Model\QuoteManagement\PaymentConfiguration $paymentConfiguration,
        \Magento\InstantPurchase\Model\QuoteManagement\Purchase $purchase
    ) {
        $this->paymentConfiguration = $paymentConfiguration;
        $this->purchase = $purchase;
    }

    /**
     * Send a payment request.
     */
    public function sendRequest($quote, $paymentData) {
        // Get the payment response
        $response = '';

        // Assign the response to self
        $this->response = $response;

        return $this;
    }

    /**
     * Create a new order.
     */
    public function createOrder($quote, $paymentResponse) {
        // Configure the payment
        $quote = $this->paymentConfiguration->configurePayment(
            $quote,
            $instantPurchaseOption->getPaymentToken()
        );

        // Place the order with payment
        $orderId = $this->purchase->purchase($quote);

        return $order;
    }

    /**
     * Chcek if a payment request is successful.
     */
    public function paymentSuccess() {
        return true;
    }
}
