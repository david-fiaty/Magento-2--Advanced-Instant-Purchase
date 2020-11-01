<?php
namespace Naxero\BuyNow\Model\Payment;

/**
 * Class PaymentHandler.
 */
class PaymentHandler
{
    /**
     * @var PaymentIntegrationFactory
     */
    public $paymentIntegrationFactory;

    /**
     * PaymentHandler constructor.
     */
    public function __construct(
        \Naxero\BuyNow\Model\Payment\Integration\PaymentIntegrationFactory $paymentIntegrationFactory
    ) {
        $this->paymentIntegrationFactory = $paymentIntegrationFactory;
    }

    /**
     * Load a payment integration instance.
     */
    public function loadMethod($code)
    {
        $classPath = $this->getIntegrationPath($code);
        return $this->paymentIntegrationFactory->create($classPath);
    }

    /**
     * Get the payment integration path.
     */
    public function getIntegrationPath($code)
    {
        $parts = explode('_', strtolower($code));
        $path = "\\Naxero\\BuyNow\\Model\\Payment\\Integration\\";
        $path .= $this->getIntegrationName($parts);
        $path .= "\\Index";

        return $path;
    }

    /**
     * Get the payment integration name.
     */
    public function getIntegrationName($parts)
    {
        $name = '';
        foreach ($parts as $part) {
            $name .= ucfirst($part);
        }

        return $name;
    }
}
