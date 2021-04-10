<?php

/**
 * Naxero.com
 * Professional ecommerce integrations for Magento.
 *
 * PHP version 7
 *
 * @category  Magento2
 * @package   Naxero
 * @author    Platforms Development Team <contact@naxero.com>
 * @copyright © Naxero.com all rights reserved
 * @license   https://opensource.org/licenses/mit-license.html MIT License
 * @link      https://www.naxero.com
 */

namespace Naxero\BuyNow\Helper;

use Naxero\BuyNow\Model\Config\Naming;

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
     * @var Object
     */
    public $pageLayoutInstance = null;

    /**
     * Class Purchase helper constructor.
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Naxero\BuyNow\Helper\Config $configHelper
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
    }

    /**
     * Write to log file.
     */
    public function write($data)
    {
        // Get the debug config value
        $debug = $this->configHelper->value('debug/debug_enabled');

        // Get the file logging config value
        $fileLogging = $this->configHelper->value('debug/file_logging_enabled');

        // Write the data to the log file
        if ($debug && $fileLogging) {
            $filePath = BP . '/var/log/' . Naming::MODULE_ALIAS() . '.log';
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
        $debug = $this->configHelper->value('debug/debug_enabled');

        // Get the UI logging
        $uiLogging = $this->configHelper->value('debug/ui_logging_enabled');
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
        $debug = $this->configHelper->value('debug/debug_enabled');

        // Get the UI logging
        $uiLogging = $this->configHelper->value('debug/ui_logging_enabled');
        if ($debug && $uiLogging) {
            return $this->getPageLayout()
            ->createBlock(Naming::MODULE_PATH() . '\Block\Debug\ErrorMessage')
            ->setTemplate(Naming::MODULE_NAME() . '::messages/error.phtml')
            ->setData('msg', $data)
            ->toHtml();
        }

        return '';
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
            Naming::MODULE_ALIAS(),
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
            Naming::MODULE_ALIAS()
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
     * Get a page factory layout instance.
     */
    public function getPageLayout()
    {
        return ($this->pageLayoutInstance)
        ? $this->pageLayoutInstance
        : $this->pageFactory->create()->getLayout();
    }
}
