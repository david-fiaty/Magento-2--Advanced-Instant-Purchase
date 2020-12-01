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
 * Class Popup helper.
 */
class Popup extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var PageFactory
     */
    public $pageFactory;

    /**
     * Popup helper class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->pageFactory = $pageFactory;
    }

    /**
     * Render a popup product box.
     */
    public function getProductBoxHtml($productId)
    {
        return $this->pageFactory->create()->getLayout()
        ->createBlock(Naming::getModulePath() . '\Block\Product\PopupBox')
        ->setTemplate(Naming::getModuleName() . '::product/popup-box.phtml')
        ->setData('is_popup', true)
        ->setData('product_id', $productId)
        ->toHtml();
    }

    /**
     * Render a popup product quantity box.
     */
    public function getQuantityBoxHtml($productId, $productQuantity)
    {
        return $this->pageFactory->create()->getLayout()
        ->createBlock(Naming::getModulePath() . '\Block\Product\Quantity')
        ->setTemplate(Naming::getModuleName() . '::product/quantity.phtml')
        ->setData('product_id', $productId)
        ->setData('product_quantity', $productQuantity)
        ->setData('is_popup', true)
        ->toHtml();
    }

    /**
     * Render a popup product countdown box.
     */
    public function getCountdownBoxHtml($productId)
    {
        return $this->pageFactory->create()->getLayout()
        ->createBlock(Naming::getModulePath() . '\Block\Product\Countdown')
        ->setTemplate(Naming::getModuleName() . '::product/countdown.phtml')
        ->setData('is_popup', false)
        ->setData('product_id', $productId)
        ->toHtml();
    }
}