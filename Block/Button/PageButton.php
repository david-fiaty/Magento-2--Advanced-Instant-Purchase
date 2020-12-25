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
 * PageButton class.
 */
class PageButton extends \Magento\Framework\View\Element\Template
{
    const MODE = 'page';

    /**
     * @var Registry
     */
    public $registry;

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
     * PageButton class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Naxero\BuyNow\Helper\Block $blockHelper,
        \Naxero\BuyNow\Helper\Config $configHelper,
        \Naxero\BuyNow\Helper\Purchase $purchaseHelper,
        \Naxero\BuyNow\Helper\Product $productHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->registry = $registry;
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
            $this->getProduct()->getId()
        );

        // Set the display mode
        $config['product']['display'] = self::MODE;

        // Check the display conditions
        $condition = $config['general']['product_view'] && $this->purchaseHelper->canDisplayButton($config);

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
        return $this->registry->registry('current_product');
    }

    /**
     * Update the product attributes data.
     */
    public function updateAttributesData($config)
    {
        return $this->blockHelper->updateAttributesData($config);
    }

    /**
     * Render a widget product countdown box.
     */
    public function getCountdownBoxHtml($config)
    {
        return $this->getLayout()
        ->createBlock(Naming::getModulePath() . '\Block\Product\Countdown')
        ->setTemplate(Naming::getModuleName() . '::product/countdown.phtml')
        ->setData('config', $config)
        ->setData('is_popup', false)
        ->toHtml();
    }
}
