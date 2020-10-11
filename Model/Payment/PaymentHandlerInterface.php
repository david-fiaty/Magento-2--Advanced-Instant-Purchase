<?php
namespace Naxero\AdvancedInstantPurchase\Model\Payment;

interface PaymentHandlerInterface
{

    public function sendRequest();

    public function getResponse();

    public function isSuccess();

}
