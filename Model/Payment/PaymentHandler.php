<?php
namespace Naxero\AdvancedInstantPurchase\Model\Payment;

class PaymentHandler implements \Naxero\AdvancedInstantPurchase\Model\Payment\PaymentHandlerInterface
{

    public function __construct(

    ) {

    }

    public function loadMethod($code) {


        return $this; 
    }
}
