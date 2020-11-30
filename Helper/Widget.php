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
 * Class Widget helper.
 */
class Widget extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var PageFactory
     */
    public $pageFactory;

    /**
     * Widget helper class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->pageFactory = $pageFactory;
    }

    /**
     * Render a widget product box.
     */
    public function getProductBoxHtml($content)
    {
        return $this->pageFactory->create()->getLayout()
        ->createBlock('Magento\Framework\View\Element\Template')
        ->setTemplate(Naming::getModuleName() . '::product/widget-box.phtml')
        ->setData('content', $content)
        ->setData('countdown_html', $this->getCountdownBoxHtml($content))
        ->setData('is_popup', false)
        ->toHtml();
    }

    /**
     * Render a widget product quantity box.
     */
    public function getQuantityBoxHtml($content, $productQuantity)
    {
        return $this->pageFactory->create()->getLayout()
        ->createBlock('Magento\Framework\View\Element\Template')
        ->setTemplate(Naming::getModuleName() . '::product/quantity-box.phtml')
        ->setData('content', $content)
        ->setData('product_quantity', $productQuantity)
        ->setData('is_popup', false)
        ->toHtml();
    }

    /**
     * Render a widget product countdown box.
     */
    public function getCountdownBoxHtml($content)
    {
        return $this->pageFactory->create()->getLayout()
        ->createBlock('Magento\Framework\View\Element\Template')
        ->setTemplate(Naming::getModuleName() . '::product/countdown-box.phtml')
        ->setData('content', $content)
        ->setData('is_popup', false)
        ->toHtml();
    }
}
