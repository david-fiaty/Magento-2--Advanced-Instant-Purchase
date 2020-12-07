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
     * Index controller class constructor.
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Naxero\BuyNow\Helper\Product $productHelper,
        \Naxero\BuyNow\Helper\Tools $toolsHelper
    ) {
        parent::__construct($context);

        $this->formKeyValidator = $formKeyValidator;
        $this->pageFactory = $pageFactory;
        $this->jsonFactory = $jsonFactory;
        $this->productHelper = $productHelper;
        $this->toolsHelper = $toolsHelper;
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
            $productId = $this->getRequest()->getParam('product_id');
            $productQuantity = $this->getRequest()->getParam('product_quantity');
            $carrierPrice = $this->getRequest()->getParam('carrier_price');

        }

        return $this->jsonFactory->create()->setData([
            'data' => $this->getTotalData($productId, $productQuantity, $carrierPrice)
        ]);
    }

    /**
     * Get the summary total data.
     */
    public function getTotalData($productId, $productQuantity, $carrierPrice)
    {
        // Product price
        $productPrice = $this->productHelper->getProductPrice(
            $productId,
            false,
            false
        );

        // Subtotal
        $subtotal = $productPrice * $productQuantity;

        // Total
        $total = $subtotal + $carrierPrice;

        return [
            'shipping' => [
                'amount' => $this->toolsHelper->renderAmount($carrierPrice, false, false),
                'rendered' => $this->toolsHelper->renderAmount($carrierPrice, true, false)
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
}
