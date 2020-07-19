<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

/**
 * Class Connfig
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CONFIG_FILE_NAME = 'config.xml';

    /**
     * @var ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * @var Parser
     */
    public $xmlParser;

    /**
     * @var Dir
     */
    public $moduleDirReader;

    /**
     * Class Config constructor
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Xml\Parser $xmlParser,
        \Magento\Framework\Module\Dir\Reader $moduleDirReader
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->xmlParser = $xmlParser;
        $this->moduleDirReader = $moduleDirReader;
    }

    /**
     * Get a module configuration value.
     */
    public function value($field)
    {
        $path = 'advanced_instant_purchase/' . $field;
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get all module configuration values.
     */
    public function getValues()
    {   
        // Load the config data
        $output = $this->xmlParser
            ->load($this->getFilePath(self::CONFIG_FILE_NAME))
            ->xmlToArray()['config']['_value']['default'];

            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/xdata.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info(print_r($output, 1));

        return $output;
    }

    /**
     * Finds a file path from file name.
     *
     * @param string $fileName
     * @return string
     */
    public function getFilePath($fileName)
    {
        return $this->moduleDirReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_ETC_DIR,
            'Naxero_AdvancedInstantPurchase'
        ) . '/' . $fileName;
    }
}
