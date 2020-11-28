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
     * @var Block
     */
    public $blockHelper;

    /**
     * Popup helper class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Naxero\BuyNow\Helper\Block $blockHelper
    ) {
        $this->pageFactory = $pageFactory;
        $this->blockHelper = $blockHelper;
    }

    /**
     * Render a popup product box.
     */
    public function getProductBoxHtml($productId, $content, $subject = null)
    {
        // Get the layout
        $layout = $subject
        ? $subject->getLayout()
        : $this->pageFactory->create()->getLayout();

        // Get the quantity box HTML
        $qtyBoxHtml = $this->blockHelper->renderQuantityBox(
            $content['config']['product']['id']
        );

        return $layout
        ->createBlock('Magento\Framework\View\Element\Template')
        ->setTemplate(Naming::getModuleName() . '::product/popup-box.phtml')
        ->setData('content', $content)
        ->setData('quantity_box_html', $qtyBoxHtml)
        ->toHtml();
    }
}