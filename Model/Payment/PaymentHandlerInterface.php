<?php
namespace Naxero\AdvancedInstantPurchase\Model\Payment;

interface PaymentHandlerInterface
{

    public function sendRequest($quote);

    public function getResponse($order, $response);

    public function isSuccess();

}
