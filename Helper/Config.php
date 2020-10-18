<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

/**
 * Class Config helper.
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
     * @var Repository
     */
    public $assetRepo; 

    /**
     * @var Product
     */
    public $productHelper;

    /**
     * @var Customer
     */
    public $customerHelper;

    /**
     * Class Config constructor.
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Xml\Parser $xmlParser,
        \Magento\Framework\Module\Dir\Reader $moduleDirReader,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Naxero\AdvancedInstantPurchase\Helper\Product $productHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->xmlParser = $xmlParser;
        $this->moduleDirReader = $moduleDirReader;
        $this->assetRepo = $assetRepo;
        $this->productHelper = $productHelper;
        $this->customerHelper = $customerHelper;
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
    public function getFrontendValues()
    {
        // Get the config values
        $values = $this->getValues();

        // Remove uneeded elements
        unset($values['card_form']);

        // Product info
        $values['product'] = $this->productHelper->getData();
        $values['isListView'] = $this->productHelper->isListView();

        // Loader icon
        $values['ui']['loader'] = $this->getLoaderIconUrl();
        
        return array_merge(
            $values,
            $this->customerHelper->getUserParams()
        );
    }

    /**
     * Can the button be displayed for out of stock products.
     */
    public function bypassOos($pid)
    {
        $productId = $this->productHelper->getProduct($pid)->getId();
        return !$this->productHelper->isInStock($productId)
        ? $this->value('products/oos_enabled')
        : true;
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