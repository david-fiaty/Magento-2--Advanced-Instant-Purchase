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
    public function getProductBoxHtml($content) {
        // Get the quantity box HTML
        $qtyBoxHtml = $this->getQuantityBoxHtml($content);

        return $this->pageFactory->create()->getLayout()
        ->createBlock('Magento\Framework\View\Element\Template')
        ->setTemplate(Naming::getModuleName() . '::product/popup-box.phtml')
        ->setData('content', $content)
        ->setData('quantity_box_html', $qtyBoxHtml)
        ->toHtml();
    }

    /**
     * Render a popup product quantity box.
     */
    public function getQuantityBoxHtml($content)
    {
        return $this->pageFactory->create()->getLayout()
        ->createBlock('Magento\Framework\View\Element\Template')
        ->setTemplate(Naming::getModuleName() . '::product/quantity-box.phtml')
        ->setData('content', $content)
        ->setData('is_popup', true)
        ->toHtml();
    }
}