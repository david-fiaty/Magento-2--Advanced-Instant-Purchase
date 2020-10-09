<?php
namespace Naxero\AdvancedInstantPurchase\Model\Service;

class CorePaymentMethod
{

    public function __construct(

    ) {

    }

    public function sendPaymentRequest($quote) {

        return $response;
    }

    public function handlePaymentResponse($order, $response) {
        return true;
    }
}
