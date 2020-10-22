<?php
namespace Naxero\AdvancedInstantPurchase\Model\Payment\Integration\Checkmo;

/**
 * Class Index.
 */
class Index implements \Naxero\AdvancedInstantPurchase\Model\Payment\Integration\PaymentIntegrationInterface
{
    /**
     * @var QuoteManagement
     */
    public $quoteManagement;

    /**
     * @var string|array|object
     */
    public $response;

    /**
     * Class Index constructor.
     */
    public function __construct(
        \Magento\Quote\Model\QuoteManagement $quoteManagement
    ) {
        $this->quoteManagement = $quoteManagement;
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
     * Chcek if a payment request is successful.
     */
    public function paymentSuccess() {
        return true;
    }

    /**
     * Create a new order.
     */
    public function createOrder($quote, $paymentResponse) { 
        $order = $this->quoteManagement->submit($quote);

        return $order;
    }
}
