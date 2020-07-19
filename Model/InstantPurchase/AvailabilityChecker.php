<?php
namespace Naxero\AdvancedInstantPurchase\Model\InstantPurchase;

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
        \Naxero\AdvancedInstantPurchase\Helper\Config $config,
        \Naxero\AdvancedInstantPurchase\Model\Service\VaultHandlerService $vaultHandler
    ) {
        $this->config = $config;
        $this->vaultHandler = $vaultHandler;
    }

    /**
     * @inheritdoc
     */
    public function isAvailable()
    {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/x.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($this->config->value('general/enabled'), 1));
        $logger->info(print_r($this->config->value('display/show_guest_button'), 1));

        return true;
    }
}
