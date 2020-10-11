<?php
namespace Naxero\AdvancedInstantPurchase\Model\Payment;

class PaymentHandler
{
    /**
     * Load a payment integration instance.
     */
    public function loadMethod($code) {
        $classPath = $this->getIntegrationPath($code);
        return new $classPath(); 
    }

    /**
     * Get the payment integration path.
     */
    public function getIntegrationPath($code) {
        $parts = explode('_', strtolower($code));
        $path = "\\Naxero\\AdvancedInstantPurchase\\Model\\Payment\\Integration\\";
        $path .= $this->getIntegrationName($parts);
        $path .= "\\Index";

        return $path;
    }

    /**
     * Get the payment integration name.
     */
    public function getIntegrationName($parts) {
        $name = '';
        foreach ($parts as $part) {
            $name .= ucfirst($part);
        }

        return $name;
    }
}
