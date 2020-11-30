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

namespace Naxero\BuyNow\Controller\Product;

use Naxero\BuyNow\Model\Config\Naming;

/**
 * Price controller class.
 */
class Price extends \Magento\Framework\App\Action\Action
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
     * PriceCurrencyInterface
     */
    public $priceCurrencyInterface;

    /**
     * Index controller class constructor.
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface,
        \Naxero\BuyNow\Helper\Product $productHelper
    ) {
        parent::__construct($context);

        $this->formKeyValidator = $formKeyValidator;
        $this->pageFactory = $pageFactory;
        $this->jsonFactory = $jsonFactory;
        $this->priceCurrencyInterface = $priceCurrencyInterface;
        $this->productHelper = $productHelper;
    }

    /**
     * Run the log data provider logic.
     *
     * @return JsonResult
     */
    public function execute()
    {
        // Prepare the output
        $html = '';

        // Process the request
        $request = $this->getRequest();
        if ($request->isAjax()) {
        // Todo - Validate request form key
        //if ($request->isAjax() && $this->formKeyValidator->validate($request)) {
            $html .= $this->renderProductPrice();
        }

        return $this->jsonFactory->create()->setData(
            ['html' => $html]
        );
    }

    /**
     * Renders a product price.
     */
    public function renderProductPrice()
    {
        // Get the request parmeters
        $productId = $this->getRequest()->getParam('product_id');
        $productQuantity = $this->getRequest()->getParam('product_quantity');

        // Get the product price
        $productPrice = $this->productHelper->getProductPrice($productId, false, false);

        // Calculate the total price
        $totalPrice = $productQuantity * $productPrice;

        // Formatted price
        $formattedPrice = $this->priceCurrencyInterface->convertAndFormat($totalPrice);

        return $formattedPrice;
    }
}
