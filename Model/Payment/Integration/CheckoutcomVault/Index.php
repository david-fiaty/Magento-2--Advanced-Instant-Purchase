<?php
namespace Naxero\AdvancedInstantPurchase\Model\Payment\Integration\CheckoutcomVault;

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
        /**
         * Send a payment request according to 
         * the specific payment processor logic
         * and assign the response payload to
         * $this->response for further processing
         * in the $this->isSuccess() method.
         */
        
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
        $order = $this->quoteManagement->submit($quote);

        return $order;
    }

    /**
     * Chcek if a payment request is successful.
     */
    public function paymentSuccess() {
        return true;
    }
}
