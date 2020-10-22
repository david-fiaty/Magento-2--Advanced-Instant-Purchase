<?php
namespace Naxero\AdvancedInstantPurchase\Controller\Ajax;

/**
 * Confirmation Class.
 */
class Confirmation extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Validator
     */
    public $formKeyValidator;

    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @var CurrentCustomer
     */
    public $currentCustomer;

    /**
     * @var PageFactory
     */
    public $pageFactory;

    /**
     * @var JsonFactory
     */
    public $jsonFactory;

    /**
     * @var Customer
     */
    public $customerHelper;

    /**
     * @var Config
     */
    public $configHelper;
    
    /**
     * BillingAddress constructor.
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper
    ) {
        parent::__construct($context);
        $this->formKeyValidator = $formKeyValidator;
        $this->customerSession = $customerSession;
        $this->customerHelper = $customerHelper;
        $this->configHelper = $configHelper;
        $this->currentCustomer = $currentCustomer;
        $this->pageFactory = $pageFactory;
        $this->jsonFactory = $jsonFactory;
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
        if ($request->isAjax()) {
            $html .= $this->loadBlock();
        }

        return $this->jsonFactory->create()->setData(
            ['html' => $html]
        );
    }

    /**
     * Generates a block.
     */
    public function loadBlock()
    {
        $html = '';
        $action = $this->getRequest()->getParam('action');
        if ($action && !empty($action)) {
            $fn =  'new' . $action . 'Block';
            if (method_exists($this, $fn)) {
                $html .= $this->$fn();
            }
        }

        return $html;
    }

    /**
     * Generates the confirmation block.
     */
    public function newConfirmationBlock()
    {
        // Confirmation content
        $html = $this->pageFactory->create()->getLayout()
            ->createBlock('Naxero\AdvancedInstantPurchase\Block\Confirmation\Data')
            ->setTemplate('Naxero_AdvancedInstantPurchase::popup/confirmation-data.phtml')
            ->toHtml();

        // Agreements
        $enableAgreements = $this->configHelper->value('general/enable_agreements');
        if ($enableAgreements) {
            $html .= $this->getAgreementsLinks();
        }

        return $html;
    }

    /**
     * Generates the new address block.
     */
    public function newAddressBlock()
    {
        return $this->pageFactory->create()->getLayout()
            ->createBlock(
                'Naxero\AdvancedInstantPurchase\Block\Address\Edit',
                'customer_address_edit',
                [
                    'customerSession' => $this->customerSession,
                    'currentCustomer' => $this->currentCustomer
                ]
            )
            ->setTemplate('Naxero_AdvancedInstantPurchase::address/edit.phtml')
            ->toHtml();
    }

    /**
     * Generates the new card block.
     */
    public function newCardBlock()
    {
        return $this->pageFactory->create()->getLayout()
        ->createBlock('Magento\Framework\View\Element\Template')
        ->setTemplate('Naxero_AdvancedInstantPurchase::popup/card.phtml')
        ->setData('load', $this->configHelper->value('card_form/load'))
        ->toHtml();
    }

    /**
     * Get the agreements links.
     */
    public function getAgreementsLinks() {
        return $this->pageFactory->create()->getLayout()
        ->createBlock('Magento\CheckoutAgreements\Block\Agreements')
        ->setTemplate('Naxero_AdvancedInstantPurchase::agreements/agreements-link.phtml')
        ->toHtml();
    }

    /**
     * Get the terms and conditions.
     */
    public function newAgreementBlock() {
        $enableAgreements = $this->configHelper->value('general/enable_agreements');
        if ($enableAgreements) {
            return $this->pageFactory->create()->getLayout()
            ->createBlock('Magento\CheckoutAgreements\Block\Agreements')
            ->setTemplate('Naxero_AdvancedInstantPurchase::/agreements/agreements-detail.phtml')
            ->toHtml();
        }

        return '';
    }
}
