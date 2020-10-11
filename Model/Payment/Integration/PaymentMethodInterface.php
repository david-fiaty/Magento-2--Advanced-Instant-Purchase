<?php
namespace Naxero\AdvancedInstantPurchase\Model\Payment\Integration;

interface PaymentMethodInterface
{
    public function sendRequest($quote, $paymentData);

    public function createOrder($quote, $paymentResponse);

    public function paymentSuccess();
}
