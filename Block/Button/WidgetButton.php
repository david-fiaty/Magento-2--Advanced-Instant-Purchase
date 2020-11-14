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

namespace Naxero\BuyNow\Block\Button;

/**
 * WidgetButton class constructor.
 */
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class WidgetButton extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    const MODE = 'widget';

    public $_template = "button/base.phtml";

    /**
     * @var Block
     */
    public $blockHelper;

    /**
     * @var Config
     */
    public $configHelper;

    /**
     * @var Purchase
     */
    public $purchaseHelper;

    /**
     * @var Product
     */
    public $productHelper;

    /**
     * WidgetButton class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Naxero\BuyNow\Helper\Block $blockHelper,
        \Naxero\BuyNow\Helper\Config $configHelper,
        \Naxero\BuyNow\Helper\Purchase $purchaseHelper,
        \Naxero\BuyNow\Helper\Product $productHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        
        $this->configHelper = $configHelper;
        $this->blockHelper = $blockHelper;
        $this->purchaseHelper = $purchaseHelper;
        $this->productHelper = $productHelper;
    }

    /**
     * Get the block config.
     */
    public function getConfig()
    {
        // Prepare the config
        $config = $this->blockHelper->getConfig(
            $this->getData('product_id')
        );

        // Set the display mode
        $config['product']['display'] = self::MODE;

        // Check the display conditions
        $condition = $config['buttons']['show_guest_button']
        && $config['general']['enabled']
        && $this->purchaseHelper->canDisplayButton();

        if ($condition) {
            // Update the product attributes data
            $config = $this->updateAttributesData($config);

            // Update the config with tag parameters
            $config = $this->updateWidgetConfig($config);

            return $config;
        }
        
        return null;
    }
    
    /**
     * Get the current product.
     */
    public function getProduct()
    {
        return $this->productHelper->getProduct(
            $this->getData('product_id')
        );
    }

    /**
     * Update the product attributes data.
     */
    public function updateAttributesData($config)
    {
        return $this->blockHelper->updateAttributesData($config, true);
    }

    /**
     * Update the config with tag parameters.
     */
    public function updateWidgetConfig($config)
    {
        var_dump($this->getData('debug_enabled'));
        exit();
        
        return $config;
    }

    /**
     * Disable the block cache.
     */
    public function getCacheLifetime()
    {
        return null;
    }
}
