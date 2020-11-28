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
     * @var PageFactory
     */
    public $pageFactory;

    /**
     * @var Widget
     */
    public $widgetHelper;

    /**
     * @var Popup
     */
    public $popupHelper;

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
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Naxero\BuyNow\Helper\Widget $widgetHelper,
        \Naxero\BuyNow\Helper\Popup $popupHelper,
        \Naxero\BuyNow\Helper\Customer $customerHelper,
        \Naxero\BuyNow\Helper\Config $configHelper,
        \Naxero\BuyNow\Helper\Product $productHelper,
        \Naxero\BuyNow\Model\Service\FilterHandlerService $filterHandler
    ) {
        $this->pageFactory = $pageFactory;
        $this->widgetHelper = $widgetHelper;
        $this->popupHelper = $popupHelper;
        $this->customerHelper = $customerHelper;
        $this->configHelper = $configHelper;
        $this->productHelper = $productHelper;
        $this->filterHandler = $filterHandler;
    }

    /**
     * Can the button be displayed for out of stock products.
     */
    public function bypassOos($productId)
    {
        return !$this->productHelper->isInStock($productId)
        ? $this->value('buttons/bypass_oos')
        : true;
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
        // Remove the card form
        unset($config['card_form']);

        // Prepare the UI loader
        $config['ui']['loader'] = $this->configHelper->getLoaderIconUrl();

        // Module title
        $config['module']['title'] = Naming::getModuleTitle();

        // Module route
        $config['module']['route'] = Naming::getModuleRoute();

        // Prepare the popup window title
        $config['popups']['popup_title'] = $this->filterHandler
        ->filterContent($config['popups']['popup_title'], $config);

        return $config;
    }

    /**
     * Render a widget product box.
     */
    public function renderWidgetProductBox($config)
    {
        return $this->widgetHelper->getProductBoxHtml(
            $this->getConfig($config['product']['id'])
        );
    }

    /**
     * Render a popup product box.
     */
    public function renderPopupProductBox($config)
    {
        return $this->popupHelper->getProductBoxHtml(
            $this->getConfig($config['product']['id'])
        );
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
            foreach ($config['product']['options'] as $option) {
                $isSwatch = $option['attribute_type'] == 'swatch';
                if ($isSwatch && $force) {
                    $option['attribute_type'] = 'select';
                }
                
                $updatedOptions[] = $option;
            }

            $config['product']['options'] = $updatedOptions;
        }

        return $config;
    }
}
