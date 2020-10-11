<?php
namespace Naxero\AdvancedInstantPurchase\Model\Payment\Integration;

interface PaymentIntegrationInterface
{
    public function sendRequest($quote, $paymentData);

    public function createOrder($quote, $paymentResponse);

    public function paymentSuccess();
}
