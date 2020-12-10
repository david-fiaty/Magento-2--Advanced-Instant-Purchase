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

namespace Naxero\BuyNow\Controller\Order;

use Naxero\BuyNow\Model\Config\Naming;

/**
 * Total controller class.
 */
class Total extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Validator
     */
    public $formKeyValidator;

    /**
     * @var PageFactory
     */
    public $pageFactory;

    /**
     * @var JsonFactory
     */
    public $jsonFactory;

    /**
     * Product
     */
    public $productHelper;

    /**
     * Tools
     */
    public $toolsHelper;

    /**
     * Order
     */
    public $orderHelper;

    /**
     * Customer
     */
    public $customerHelper;


    /**
     * ShippingSelector
     */
    public $shippingSelector;

    /**
     * Index controller class constructor.
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Naxero\BuyNow\Helper\Product $productHelper,
        \Naxero\BuyNow\Helper\Tools $toolsHelper,
        \Naxero\BuyNow\Helper\Order $orderHelper,
        \Naxero\BuyNow\Helper\Customer $customerHelper,
        \Naxero\BuyNow\Model\Order\ShippingSelector $shippingSelector
    ) {
        parent::__construct($context);

        $this->formKeyValidator = $formKeyValidator;
        $this->pageFactory = $pageFactory;
        $this->jsonFactory = $jsonFactory;
        $this->productHelper = $productHelper;
        $this->toolsHelper = $toolsHelper;
        $this->orderHelper = $orderHelper;
        $this->customerHelper = $customerHelper;
        $this->shippingSelector = $shippingSelector;
    }

    /**
     * Run the log data provider logic.
     *
     * @return JsonResult
     */
    public function execute()
    {
        // Prepare the output
        $data = '';

        // Process the request
        $request = $this->getRequest();
        if ($request->isAjax()) {
        // Todo - Validate request form key
        //if ($request->isAjax() && $this->formKeyValidator->validate($request)) {
            $data  = $this->getTotalData();
        }

        return $this->jsonFactory->create()->setData([
            'data' => $data
        ]);
    }

    /**
     * Get the summary total data.
     */
    public function getTotalData()
    {
        // Request data
        $productId = $this->getRequest()->getParam('product_id');
        $productQuantity = $this->getRequest()->getParam('product_quantity');
        $carrierCode = $this->getRequest()->getParam('carrier_code');
        $carrierCode = $this->getRequest()->getParam('carrier_code');
        $couponCode = $this->getRequest()->getParam('coupon_code');

        // Discount data
        $discountDdata = [];

        // Product price
        $productPrice = $this->productHelper->getProductPrice(
            $productId,
            false,
            false
        );

        // Subtotal
        $subtotal = $productPrice * $productQuantity;

        // Discount
        if (!empty($couponCode)) {
            $couponRule = $this->orderHelper->getCouponRule($couponCode);
            if ($couponRule && $couponRule->getIsActive() == 1) {
                // Discounted total
                $discountedTotal = $this->orderHelper->applyDiscount($couponRule, $subtotal);

                // Discount data
                $discountData = $this->getCouponRuleData($couponRule);

                // Update the subtotal
                $subtotal = $discountedTotal;
            }
        }

        // Get carrier price
        $carrier = $this->shippingSelector->getCarrierData(
            $carrierCode,
            $this->customerHelper->getCustomer()
        );

        // Total
        $total = $subtotal + $carrier['carrier_price'];

        return [
            'discount' => $discountData,
            'shipping' => [
                'amount' => $this->toolsHelper->renderAmount($carrier['carrier_price'], false, false),
                'rendered' => $this->toolsHelper->renderAmount($carrier['carrier_price'], true, false)
            ],
            'subtotal' => [
                'amount' => $this->toolsHelper->renderAmount($subtotal, false, false),
                'rendered' => $this->toolsHelper->renderAmount($subtotal, true, false)
            ],
            'total' => [
                'amount' => $this->toolsHelper->renderAmount($total, false, false),
                'rendered' => $this->toolsHelper->renderAmount($total, true, false)
            ]
        ];
    }

    /**
     * Get a coupon code data.
     */
    public function getCouponRuleData($rule)
    {
        $discountAmount = $rule->getDiscountAmount();

        return [
            'id' => $rule->getRuleId(),
            'name' => $rule->getName(),
            'amount' => $discountAmount,
            'rendered' => $this->toolsHelper->renderAmount($discountAmount, true, false),
            'description' => $rule->getDescription(),
        ];
    }
}
