<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

/**
 * Class Logger Helper.
 */
class Logger extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ManagerInterface
     */
    public $messageManager;
    
    /**
     * @var Config
     */
    public $configHelper;

    /**
     * Class Purchase helper constructor.
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper
    ) {
        $this->messageManager = $messageManager;
        $this->configHelper = $configHelper;
    }

    /**
     * Write to log file.
     *
     * @param mixed $msg The message
     */
    public function write($msg)
    {
        // Get the debug config value
        $debug = $this->configHelper->value('general/debug_enabled');

        // Get the file logging config value
        $fileLogging = $this->configHelper->value('general/file_logging_enabled');

        // Handle the file logging
        if ($debug && $fileLogging) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/naxero_buynow.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($msg);
        }
    }

    /**
     * Display the debug information on the front end.
     *
     * @param mixed $response The response
     */
    public function display($msg)
    {
        // Get the debug config value
        $debug = $this->configHelper->value('general/debug_enabled');

        // Get the UI logging
        $uiLogging = $this->configHelper->value('general/ui_logging_enabled');
        if ($debug && $uiLogging) {
            $this->messageManager->addNotice($msg);
        }
    }
}