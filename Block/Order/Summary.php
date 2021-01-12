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
     * Render a product coupon box.
     */
    public function getCouponBoxHtml($config)
    {
        return $this->getLayout()
        ->createBlock(Naming::getModulePath() . '\Block\Order\Coupon')
        ->setTemplate(Naming::getModuleName() . '::order/coupon.phtml')
        ->setData('config', $config)
        ->toHtml();
    }

    /**
     * Get the summary total.
     */
    public function getTotal($data, $productQuantity, $shippingRate)
    {
        // Product price
        $productPrice = $this->productHelper->getProductPrice(
            $data['config']['product']['id'],
            false,
            false
        );

        // Sub total
        $subTotal = $productPrice * $productQuantity;

        // Total
        $total = $subTotal + $shippingRate;

        return $this->toolsHelper->renderAmount($total, true, false);
    }

    /**
     * Get a selected attribute data.
     */
    public function getSelectedAttributeData($attribute, $data)
    {

    }

    /**
     * Get a selected option data.
     */
    public function getSelectedOptionData($option, $requestParams)
    {
        if (isset($requestParams['options'][$option['option_id']])) {
            return array_merge(
                $option,
                [
                    'selected_value' => $requestParams['options'][$option['option_id']]
                ]
            );
        }


        return $option;
    }

}
