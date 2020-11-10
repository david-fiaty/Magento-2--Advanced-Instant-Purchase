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
 * BlockButton class constructor.
 */
class BlockButton extends \Magento\Framework\View\Element\Template
{
    const MODE = 'block';
    
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
     * BlockButton class constructor.
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
            return $this->updateAttributesData($config);
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
        // Prepare parameters
        $force = false;
        $swatchAsSelect = $config['products']['swatch_as_select'];

        // Update the attribute display parameters
        if ($config['product']['has_options']) {
            foreach ($config['product']['options'] as $option) {
                $isSwatch = $option['attribute_type'] == 'swatch';
                if ($isSwatch && ($swatchAsSelect || $force)) {
                    $option['attribute_type'] == 'select';
                }
            }
        }
    }

    /**
     * Disable the block cache.
     */
    public function getCacheLifetime()
    {
        return null;
    }
}
