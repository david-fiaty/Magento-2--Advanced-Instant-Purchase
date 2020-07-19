<?php
namespace Naxero\InstantPurchase\Model\InstantPurchase;

/**
 * Class AvailabilityChecker
 */
class AvailabilityChecker
{
    /**
     * @var Config
     */
    public $config;

    /**
     * @var VaultHandlerService
     */
    private $vaultHandler;

    /**
     * AvailabilityChecker constructor
     */
    public function __construct(
        \Naxero\InstantPurchase\Helper\Config $config,
        \Naxero\InstantPurchase\Model\Service\VaultHandlerService $vaultHandler
    ) {
        $this->config = $config;
        $this->vaultHandler = $vaultHandler;
    }

    /**
     * @inheritdoc
     */
    public function isAvailable()
    {


        $var = $this->config->value('general', 'enabled');

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($var, 1));

        return $this->config->value('general', 'enabled');
    }
}
