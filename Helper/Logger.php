<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

use Naxero\AdvancedInstantPurchase\Model\Config\Naming;

/**
 * Class Logger Helper.
 */
class Logger extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var PageFactory
     */
    public $pageFactory;

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
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper
    ) {
        $this->pageFactory = $pageFactory;
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

    /**
     * Renders a UI message.
     */
    public function renderUiMessage($msg)
    {
        // Get the debug config value
        $debug = $this->configHelper->value('general/debug_enabled');

        // Get the UI logging
        $uiLogging = $this->configHelper->value('general/ui_logging_enabled');
        if ($debug && $uiLogging) {
            return $this->pageFactory->create()->getLayout()
            ->createBlock('Magento\Framework\View\Element\Template')
            ->setTemplate(Naming::getModuleName() . '::messages/error.phtml')
            ->setData('msg', $msg)
            ->toHtml();
        }

        return '';
    }

    public function renderDataTree($data) {
        return $this->pageFactory->create()->getLayout()
        ->createBlock('Magento\Framework\View\Element\Template')
        ->setTemplate(Naming::getModuleName() . '::messages/ui-logger.phtml')
        ->setData('data', $data)
        ->setData('title', Naming::getModuleTitle())
        ->toHtml();  
    }
}