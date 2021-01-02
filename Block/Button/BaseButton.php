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
 * BaseButton class.
 */
class BaseButton extends \Magento\Catalog\Block\Product\ProductList\Item\Block
{
    /**
     * @var Block
     */
    public $blockHelper;

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
        \Naxero\BuyNow\Helper\Purchase $purchaseHelper,
        \Naxero\BuyNow\Helper\Product $productHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->blockHelper = $blockHelper;
        $this->purchaseHelper = $purchaseHelper;
        $this->productHelper = $productHelper;
    }


}
