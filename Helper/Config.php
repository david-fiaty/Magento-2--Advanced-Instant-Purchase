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
 * Class Config helper.
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var string
     */
    const CONFIG_FILE_NAME = 'config.xml';

    /**
     * @var Repository
     */
    public $assetRepository;

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
        \Magento\Framework\View\Asset\Repository $assetRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Xml\Parser $xmlParser,
        \Magento\Framework\Module\Dir\Reader $moduleDirReader
    ) {
        $this->assetRepository = $assetRepository;
        $this->scopeConfig = $scopeConfig;
        $this->xmlParser = $xmlParser;
        $this->moduleDirReader = $moduleDirReader;
    }

    /**
     * Get a module configuration value.
     */
    public function value($field, $core = false)
    {
        $path = !$core ? 'buy_now/' . $field : $field;
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
        $configData = $this->getConfigFieldsList();

        // Update the array with database values
        foreach ($configData as $group => $fields) {
            $output[$group] = [];
            foreach ($fields as $key => $value) {
                $v = $this->value($group . '/' . $key);
                $output[$group][$key] = $this->toBooleanFilter($v);
            }
        }

        return $output;
    }

    /**
     * Get the config fields array.
     */
    public function getConfigFieldsList()
    {
        return $this->xmlParser
        ->load($this->getFilePath(self::CONFIG_FILE_NAME))
        ->xmlToArray()['config']['_value']['default']['buy_now'];
    }

    /**
     * Convert a value to boolean.
     */
    public function toBooleanFilter($value)
    {
        return $value == '1' || $value == '0'
        ? filter_var($value, FILTER_VALIDATE_BOOLEAN)
        : $value;
    }

    /**
     * Get the loader icon URL.
     */
    public function getLoaderIconUrl()
    {
        return $this->assetRepository->getUrl(Naming::MODULE_NAME() . '::images/ajax-loader.gif');
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
            Naming::MODULE_NAME()
        ) . '/' . $fileName;
    }
}
