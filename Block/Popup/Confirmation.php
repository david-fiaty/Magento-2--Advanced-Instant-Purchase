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

namespace Naxero\BuyNow\Block\Popup;

use Naxero\BuyNow\Model\Config\Naming;

/**
 * Confirmation class constructor.
 */
class Confirmation extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Block
     */
    public $blockHelper;

    /**
     * Confirmation class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Naxero\BuyNow\Helper\Block $blockHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->blockHelper = $blockHelper;
    }

    /**
     * Render a popup product box.
     */
    public function getProductBoxHtml($config)
    {
        return $this->getLayout()
        ->createBlock(Naming::getModulePath() . '\Block\Product\PopupBox')
        ->setTemplate(Naming::getModuleName() . '::product/popup-box.phtml')
        ->setData('is_popup', true)
        ->setData('config', $config)
        ->toHtml();
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
     * Render a popup product countdown box.
     */
    public function getCountdownBoxHtml($config)
    {
        return $this->getLayout()
        ->createBlock(Naming::getModulePath() . '\Block\Product\Countdown')
        ->setTemplate(Naming::getModuleName() . '::product/countdown.phtml')
        ->setData('is_popup', false)
        ->setData('config', $config)
        ->toHtml();
    }
}
