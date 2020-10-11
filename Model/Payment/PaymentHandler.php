<?php
namespace Naxero\AdvancedInstantPurchase\Model\Payment;

class PaymentHandler implements \Naxero\AdvancedInstantPurchase\Model\Payment\PaymentHandlerInterface
{

    public function __construct(

    ) {

    }

    public function loadMethod($code) {
        $classPath = $this->getClassPath($code);
        return new $classPath(); 
    }

    public function getClassPath($code) {
        $parts = explode('_', strtolower($code));
        $path = "\\Naxero\\AdvancedInstantPurchase\\Model\\Payment\\Integration";
        foreach ($parts as $part) {
            $path .= "\\" . ucfirst($part);
        }
        $path .= "\\Index";

        return $path;
    }
}
