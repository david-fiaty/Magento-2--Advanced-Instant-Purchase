<?php
namespace Naxero\AdvancedInstantPurchase\Model\Payment\Integration;

interface PaymentMethodInterface
{

    public function sendRequest();

    public function getResponse();

    public function isSuccess();

}
