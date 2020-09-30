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
     * @var Session
     */
    public $customerSession;

    /**
     * @var Repository
     */
    public $assetRepo; 

    /**
     * Class Config constructor.
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Xml\Parser $xmlParser,
        \Magento\Framework\Module\Dir\Reader $moduleDirReader,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->xmlParser = $xmlParser;
        $this->moduleDirReader = $moduleDirReader;
        $this->customerSession = $customerSession;
        $this->assetRepo = $assetRepo;
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
        // Get the config values
        $values = $this->getValues();

        // Remove uneeded elements
        unset($values['card_form']);

        // Add user connection status
        $values['user']['connected'] = $this->customerSession->isLoggedIn();

        // Loader icon
        $values['ui']['loader'] = $this->getLoaderIconUrl();
        
        return $values;
    }

    /**
     * Get the loader icon URL.
     */
    public function getLoaderIconUrl()
    {
        return $this->assetRepo->getUrl('Naxero_AdvancedInstantPurchase::images/ajax-loader.gif');
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