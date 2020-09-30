<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

/**
 * Class Config.
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
     * Class Config constructor.
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
    public function value($field, $core = false)
    {
        $path = !$core ? 'advanced_instant_purchase/' . $field : $field;
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
        $output = [];
        $configData = $this->xmlParser
            ->load($this->getFilePath(self::CONFIG_FILE_NAME))
            ->xmlToArray()['config']['_value']['default']['advanced_instant_purchase'];

        // Update the array with database values
        foreach ($configData as $group => $fields) {
            $output[$group] = [];
            foreach ($fields as $key => $value) {
                $output[$group][$key] = $this->value($group . '/' . $key);
            }
        }

        return $output;
    }

    /**
     * Get filtered config values for the frontend.
     */
    public function getFilteredValues()
    {
        $values = $this->getValues();
        unset($values['card_form']);

        return $values;
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