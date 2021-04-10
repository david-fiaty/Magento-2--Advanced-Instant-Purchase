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
 * Class Block helper.
 */
class Block extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Context
     */
    public $httpContext;

    /**
     * @var PageFactory
     */
    public $pageFactory;

    /**
     * @var Customer
     */
    public $customerHelper;

    /**
     * @var Config
     */
    public $configHelper;

    /**
     * @var Product
     */
    public $productHelper;

    /**
     * @var FilterHandlerService
     */
    public $filterHandler;

    /**
     * Block helper class constructor.
     */
    public function __construct(
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Naxero\BuyNow\Helper\Customer $customerHelper,
        \Naxero\BuyNow\Helper\Config $configHelper,
        \Naxero\BuyNow\Helper\Product $productHelper,
        \Naxero\BuyNow\Model\Service\FilterHandlerService $filterHandler
    ) {
        $this->httpContext = $httpContext;
        $this->pageFactory = $pageFactory;
        $this->customerHelper = $customerHelper;
        $this->configHelper = $configHelper;
        $this->productHelper = $productHelper;
        $this->filterHandler = $filterHandler;
    }

    /**
     * Get the Buy Now button text.
     */
    public function getButtonText()
    {
        // Get the module config
        $config = $this->configHelper->getValues();

        // Get logged in status
        $isLoggedIn = $this->customerHelper->getUserParams()['user']['connected'];

        // Return the button text
        return $isLoggedIn
        ? $config['buttons']['button_text']
        : $config['buttons']['guest_button_text'];
    }

    /**
     * Get the block customer data.
     */
    public function getCustomerData()
    {
        return $this->httpContext->getValue('customer_data');
    }

    /**
     * Get a block configuration parameters.
     */
    public function getConfig($productId)
    {
        // Get the config values
        $config = $this->configHelper->getValues();

        // Prepare the block config data
        $output = $config
        + $this->configHelper->getValues()
        + ['product' => $this->productHelper->getData($productId)]
        + $this->customerHelper->getUserParams();

        // Prepare the block parameters
        $output = $this->prepareBlockConfig($output);

        return $output;
    }

    /**
     * Prepare the block config parameters.
     */
    public function prepareBlockConfig($config)
    {
        // Prepare the UI loader
        $config['ui']['loader'] = $this->configHelper->getLoaderIconUrl();

        // Module title
        $config['module']['title'] = Naming::MODULE_TITLE();

        // Module route
        $config['module']['route'] = Naming::MODULE_ROUTE();

        // Prepare the popup window title
        $config['popups']['popup_title'] = $this->filterHandler
        ->filterContent($config['popups']['popup_title'], $config);

        return $config;
    }

    /**
     * Update the product attributes data.
     */
    public function updateAttributesData($config, $force = false)
    {
        // Prepare parameters
        $updatedOptions = [];

        // Update the attribute display parameters
        if ($config['product']['has_options']) {
            foreach ($config['product']['attributes'] as $option) {
                $isSwatch = $option['attribute_type'] == 'swatch';
                if ($isSwatch && $force) {
                    $option['attribute_type'] = 'select';
                }

                $updatedOptions[] = $option;
            }

            $config['product']['attributes'] = $updatedOptions;
        }

        return $config;
    }
}
