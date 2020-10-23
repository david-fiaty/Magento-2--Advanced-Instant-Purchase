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
        $debug = $this->scopeConfig->getValue(
            'settings/checkoutcom_configuration/debug',
            ScopeInterface::SCOPE_STORE
        );

        // Get the file logging config value
        $fileLogging = $this->scopeConfig->getValue(
            'settings/checkoutcom_configuration/file_logging',
            ScopeInterface::SCOPE_STORE
        );

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
    public function display($response)
    {
        // Get the debug config value
        $debug = $this->scopeConfig->getValue(
            'settings/checkoutcom_configuration/debug',
            ScopeInterface::SCOPE_STORE
        );

        // Get the gateway response config value
        $gatewayResponses = $this->scopeConfig->getValue(
            'settings/checkoutcom_configuration/gateway_responses',
            ScopeInterface::SCOPE_STORE
        );

        if ($debug && $gatewayResponses) {
            $output = json_encode($response);
            $this->messageManager->addComplexSuccessMessage(
                'ckoMessages',
                ['output' => $output]
            );
        }
    }
}