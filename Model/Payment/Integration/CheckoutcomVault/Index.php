<?php
namespace Naxero\AdvancedInstantPurchase\Model\Payment\Integration\CheckoutcomVault;

class Index implements \Naxero\AdvancedInstantPurchase\Model\Payment\Integration\PaymentMethodInterface
{
    public function sendRequest($quote, $paymentData) {
        /**
         * Send a payment request according to 
         * the specific payment processor logic
         * and assign the response payload to
         * $this->response for further processing
         * in the $this->isSuccess() method.
         */
        
        $this->response = '';

        return $this;
    }

    public function isSuccess() {
        /**
         * Retrieve the payment response payload
         * in $this->response and check if the payment is successful
         * according to the specific payment processor logic.
         */

        return true;
    }
}
