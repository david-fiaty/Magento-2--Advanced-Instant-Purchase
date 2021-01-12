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
     * Render a popup summary box.
     */
    public function getSummaryBoxHtml($data, $productQuantity, $params)
    {
        return $this->getLayout()
        ->createBlock(Naming::getModulePath() . '\Block\Order\Summary')
        ->setTemplate(Naming::getModuleName() . '::order/summary.phtml')
        ->setData('data', $data)
        ->setData('params', $params)
        ->setData('product_quantity', $productQuantity)
        ->toHtml();
    }
}
