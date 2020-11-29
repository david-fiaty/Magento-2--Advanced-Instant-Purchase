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
 * Confirmation controller class
 */
class Confirmation extends \Magento\Framework\App\Action\Action
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
     * @var Config
     */
    public $configHelper;

    /**
     * @var Purchase
     */
    public $purchaseHelper;
 
    /**
     * @var Product
     */
    public $productHelper;

    /**
     * Confirmation controller class constructor
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Naxero\BuyNow\Helper\Config $configHelper,
        \Naxero\BuyNow\Helper\Purchase $purchaseHelper,
        \Naxero\BuyNow\Helper\Product $productHelper
    ) {
        parent::__construct($context);
        
        $this->formKeyValidator = $formKeyValidator;
        $this->pageFactory = $pageFactory;
        $this->jsonFactory = $jsonFactory;
        $this->configHelper = $configHelper;
        $this->purchaseHelper = $purchaseHelper;
        $this->productHelper = $productHelper;
    }

    /**
     * Handles the controller method.
     */
    public function execute()
    {
        // Prepare the output
        $html = '';

        // Process the request
        $request = $this->getRequest();
        if ($request->isAjax() && $this->formKeyValidator->validate($request)) {
            $html .= $this->newConfirmationBlock();
        }

        return $this->jsonFactory->create()->setData(
            ['html' => $html]
        );
    }

    /**
     * Generates the confirmation block.
     */
    public function newConfirmationBlock()
    {
        // Get the product id from request
        $productId = (int) $this->getRequest()->getParam('product_id');

        // Confirmation content
        $html = '';
        if ($productId > 0) {
            $html = $this->pageFactory->create()->getLayout()
                ->createBlock(Naming::getModulePath() . '\Block\Screen\Confirmation')
                ->setTemplate(Naming::getModuleName() . '::popup/confirmation.phtml')
                ->setData('content', $this->purchaseHelper->getConfirmContent($productId))
                ->toHtml();

            // Agreements
            $enableAgreements = $this->configHelper->value('popups/popup_enable_agreements');
            if ($enableAgreements) {
                $html .= $this->getAgreementsLinks();
            }
        }

        return $html;
    }


    /**
     * Get the product quantity.
     */
    public function getProductQuantity()
    {
        // Get the request parameters
        $productQuantity = (int) $this->getRequest()->getParam('product_quantity');
        $productId = (int) $this->getRequest()->getParam('product_id');
        
        // Get the quantity limits
        $quantityLimits = $this->productHelper->getQuantityLimits($productId);

        // Determine the value
        $condition = $productQuantity > 0
        && $productQuantity >= $quantityLimits['min']
        && $productQuantity <= $quantityLimits['max'];

        return $condition ? $productQuantity : $quantityLimits['min'];
    }

    /**
     * Get the agreements links.
     */
    public function getAgreementsLinks()
    {
        return $this->pageFactory->create()->getLayout()
            ->createBlock('Magento\CheckoutAgreements\Block\Agreements')
            ->setTemplate(Naming::getModuleName() . '::agreements/agreements-link.phtml')
            ->toHtml();
    }

    /**
     * Get the terms and conditions.
     */
    public function newAgreementBlock()
    {
        $enableAgreements = $this->configHelper->value('popups/popup_enable_agreements');
        if ($enableAgreements) {
            return $this->pageFactory->create()->getLayout()
                ->createBlock('Magento\CheckoutAgreements\Block\Agreements')
                ->setTemplate(Naming::getModuleName() . '::agreements/agreements-detail.phtml')
                ->toHtml();
        }

        return '';
    }
}
