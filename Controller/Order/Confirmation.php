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
        \Naxero\BuyNow\Helper\Purchase $purchaseHelper
    ) {
        parent::__construct($context);
        
        $this->formKeyValidator = $formKeyValidator;
        $this->pageFactory = $pageFactory;
        $this->jsonFactory = $jsonFactory;
        $this->configHelper = $configHelper;
        $this->purchaseHelper = $purchaseHelper;
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
                ->setTemplate(Naming::getModuleName() . '::popup/confirmation-data.phtml')
                ->setData('content', $this->purchaseHelper->getConfirmContent($productId))
                ->toHtml();

            // Agreements
            $enableAgreements = $this->configHelper->value('general/enable_agreements');
            if ($enableAgreements) {
                $html .= $this->getAgreementsLinks();
            }
        }

        return $html;
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
        $enableAgreements = $this->configHelper->value('general/enable_agreements');
        if ($enableAgreements) {
            return $this->pageFactory->create()->getLayout()
                ->createBlock('Magento\CheckoutAgreements\Block\Agreements')
                ->setTemplate(Naming::getModuleName() . '::agreements/agreements-detail.phtml')
                ->toHtml();
        }

        return '';
    }
}
