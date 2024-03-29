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

use Naxero\BuyNow\Model\Config\Naming;

/**
 * ListButton class.
 */
class ListButton extends \Magento\Catalog\Block\Product\ProductList\Item\Block
{
    const MODE = 'list';

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
     * ListButton class constructor.
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Naxero\BuyNow\Helper\Block $blockHelper,
        \Naxero\BuyNow\Helper\Config $configHelper,
        \Naxero\BuyNow\Helper\Purchase $purchaseHelper,
        \Naxero\BuyNow\Helper\Product $productHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->blockHelper = $blockHelper;
        $this->configHelper = $configHelper;
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
            $this->getProduct()->getId()
        );

        // Update the customer data
        $config['user'] = array_merge(
            $config['user'],
            $this->blockHelper->getCustomerData()
        );
        
        // Update the product attributes data
        $config = $this->updateAttributesData($config);
        
        // Set the display mode
        $config['product']['display'] = self::MODE;

        // Check the display conditions
        $condition = $config['general']['product_list']
        && $this->purchaseHelper->canDisplayButton($config);

        if ($condition) {
            return $config;
        }

        return null;
    }

    /**
     * Get the current product.
     */
    public function getProduct()
    {
        return parent::getProduct();
    }

    /**
     * Update the product attributes data.
     */
    public function updateAttributesData($config)
    {
        return $this->blockHelper->updateAttributesData($config);
    }
}
