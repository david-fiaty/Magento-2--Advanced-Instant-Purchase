<?php
namespace Naxero\AdvancedInstantPurchase\Model\Payment\Integration;

/**
 * Interface PaymentIntegrationInterface.
 */
interface PaymentIntegrationInterface
{
    /**
     * Send a payment request.
     *
     * @param Quote $quote
     * @param array $paymentData
     */
    public function sendRequest($quote, $paymentData);

    /**
     * Create an order.
     *
     * @param Quote $quote
     * @param string|array|object $paymentResponse
     */
    public function createOrder($quote, $paymentResponse);

    /**
     * Check if a payment is successful.
     */
    public function paymentSuccess();
}
