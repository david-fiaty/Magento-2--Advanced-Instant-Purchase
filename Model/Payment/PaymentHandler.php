<?php
namespace Naxero\AdvancedInstantPurchase\Model\Payment;

class PaymentHandler implements \Naxero\AdvancedInstantPurchase\Model\Payment\PaymentHandlerInterface
{

    public function __construct(

    ) {

    }

    public function sendRequest() {

    }

    public function getResponse() {
        $response = '';
        return $response;
    }

    public function isSuccess() {
        return true;
    }
}
