<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

use Naxero\AdvancedInstantPurchase\Model\Config\Naming;

/**
 * Class Logger Helper.
 */
class Logger extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Session
     */
    public $customerSession;

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
     * @var Objec
     */
    public $pageFactoryInstance = null;

    /**
     * Class Purchase helper constructor.
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper
    ) {
        $this->customerSession = $customerSession;
        $this->pageFactory = $pageFactory;
        $this->messageManager = $messageManager;
        $this->configHelper = $configHelper;
    }

    /**
     * Handle data logging.
     */
    public function log($data)
    {
        // Prepare the data
        $data = $this->prepareData($data); 

        // File logging
        $this->write($data);
        
        // UI logging
        // Todo - Check if other non button UI logging cases are needed
        //$this->display($data);
    }

    /**
     * Write to log file.
     */
    public function write($data)
    {
        // Get the debug config value
        $debug = $this->configHelper->value('general/debug_enabled');

        // Get the file logging config value
        $fileLogging = $this->configHelper->value('general/file_logging_enabled');

        // Write the data to the log file
        if ($debug && $fileLogging) {
            $filePath = BP . '/var/log/'. Naming::getModuleAlias() . '.log';
            $writer = new \Zend\Log\Writer\Stream($filePath);
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->debug($data);
        }
    }

    /**
     * Display the debug information on the front end.
     */
    public function display($data)
    {
        // Get the debug config value
        $debug = $this->configHelper->value('general/debug_enabled');

        // Get the UI logging
        $uiLogging = $this->configHelper->value('general/ui_logging_enabled');
        if ($debug && $uiLogging) {
            $this->messageManager->addNotice($data);
        }
    }

    /**
     * Renders a UI message.
     */
    public function renderUiMessage($data)
    {
        // Get the debug config value
        $debug = $this->configHelper->value('general/debug_enabled');

        // Get the UI logging
        $uiLogging = $this->configHelper->value('general/ui_logging_enabled');
        if ($debug && $uiLogging) {
            return $this->getPageFactory()->getLayout()
            ->createBlock('Magento\Framework\View\Element\Template')
            ->setTemplate(Naming::getModuleName() . '::messages/error.phtml')
            ->setData('msg', $data)
            ->toHtml();
        }

        return '';
    }

    /**
     * Renders a browsable data tree.
     */
    public function renderDataTree($data, $config) {
        return $this->getPageFactory()->getLayout()
        ->createBlock('Magento\Framework\View\Element\Template')
        ->setTemplate(Naming::getModuleName() . '::messages/ui-logger.phtml')
        ->setData('data', $data)
        ->setData('config', $config)
        ->setData('title', Naming::getModuleTitle())
        ->toHtml();  
    }

    /**
     * Add log data to user session.
     */
    public function addSessionData($data)
    {
        // Get teh session log data
        $currentData = $this->getSessionLogData();

        // Store the data
        $currentData['logs'][] = $data;
        $this->customerSession->setData(
            Naming::getModuleAlias(),
            $currentData
        );
    }

    /**
     * Get the user session log data.
     */
    public function getSessionLogData()
    {
        // Get the current session data
        $currentData = $this->customerSession->getData(
            Naming::getModuleAlias()
        );

        // Check for existing data
        $hasData = is_array($currentData) && !empty($currentData)
        && isset($currentData['logs']) && !empty($currentData['logs']);

        return $hasData ? $currentData : ['logs' => []];
    }

    /**
     * Prepare data for logging.
     */
    public function prepareData($data)
    {
        return is_array($data) || is_object($data) 
        ? json_encode($data) : $data;
    }

    /**
     * Get a page factory instance.
     */
    public function getPageFactory()
    {
        return ($this->pageFactoryInstance)
        ? $this->pageFactoryInstance
        : $this->pageFactory->create();
    }
}