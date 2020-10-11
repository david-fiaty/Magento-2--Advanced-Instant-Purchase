<?php
namespace Naxero\AdvancedInstantPurchase\Model\Payment\Integration\CheckoutcomVault;

class Index implements \Naxero\AdvancedInstantPurchase\Model\Payment\Integration\PaymentMethodInterface
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
