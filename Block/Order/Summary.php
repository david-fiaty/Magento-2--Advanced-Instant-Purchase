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

namespace Naxero\BuyNow\Block\Order;

use Naxero\BuyNow\Model\Config\Naming;

/**
 * Summary class constructor.
 */
class Summary extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Tools
     */
    public $toolsHelper;

    /**
     * @var Block
     */
    public $blockHelper;

    /**
     * @var Product
     */
    public $productHelper;

    /**
     * Summary class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Naxero\BuyNow\Helper\Tools $toolsHelper,
        \Naxero\BuyNow\Helper\Block $blockHelper,
        \Naxero\BuyNow\Helper\Product $productHelper,
        array $data = []
    ) {

        parent::__construct($context, $data);

        $this->toolsHelper = $toolsHelper;
        $this->blockHelper = $blockHelper;
        $this->productHelper = $productHelper;
    }

    /**
     * Render a popup product quantity box.
     */
    public function getQuantityBoxHtml($config, $productQuantity)
    {
        return $this->getLayout()
        ->createBlock(Naming::getModulePath() . '\Block\Product\Quantity')
        ->setTemplate(Naming::getModuleName() . '::product/quantity.phtml')
        ->setData('config', $config)
        ->setData('product_quantity', $productQuantity)
        ->setData('is_popup', true)
        ->toHtml();
    }

    /**
     * Render a widget product price box.
     */
    public function getPriceBoxHtml($config, $productQuantity)
    {
        return $this->getLayout()
        ->createBlock(Naming::getModulePath() . '\Block\Product\Price')
        ->setTemplate(Naming::getModuleName() . '::product/price.phtml')
        ->setData('config', $config)
        ->setData('product_quantity', $productQuantity)
        ->toHtml();
    }

    /**
     * Get the summary total.
     */
    public function getTotal($config, $productQuantity)
    {
        $productPrice = $this->productHelper->renderProductPrice($productId, $productQuantity)
    }
}
