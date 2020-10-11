<?php
namespace Naxero\AdvancedInstantPurchase\Model\Payment\Integration\CheckoutcomVault;

/**
 * Class Index.
 */
class Index implements \Naxero\AdvancedInstantPurchase\Model\Payment\Integration\PaymentMethodInterface
{
    /**
     * @var string|array|object
     */
    public $response;

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
     * Chcek if a payment request is successful.
     */
    public function isSuccess() {
        /**
         * Retrieve the payment response payload
         * in $this->response and check if the payment is successful
         * according to the specific payment processor logic.
         */

        return true;
    }
}
