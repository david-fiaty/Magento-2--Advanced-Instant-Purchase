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

namespace Naxero\BuyNow\Block\Product;

/**
 * Price class constructor.
 */
class Price extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Block
     */
    public $blockHelper;

    /**
     * @var Product
     */
    public $productHelper;

    /**
     * Price class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Naxero\BuyNow\Helper\Block $blockHelper,
        \Naxero\BuyNow\Helper\Product $productHelper,
        array $data = []
    ) {

        parent::__construct($context, $data);

        $this->blockHelper = $blockHelper;
        $this->productHelper = $productHelper;
    }
}

