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
     * @var Context
     */
    public $httpContext;

    /**
     * @var Block
     */
    public $blockHelper;

    /**
     * @var Purchase
     */
    public $purchaseHelper;

    /**
     * ListButton class constructor.
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Naxero\BuyNow\Helper\Block $blockHelper,
        \Naxero\BuyNow\Helper\Purchase $purchaseHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

    	$this->httpContext = $httpContext;
        $this->blockHelper = $blockHelper;
        $this->purchaseHelper = $purchaseHelper;
    }

    public function getCustomerData() {
        return $this->httpContext->getValue('customer_id');
    }
}
