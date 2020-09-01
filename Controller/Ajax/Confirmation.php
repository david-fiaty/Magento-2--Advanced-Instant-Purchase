<?php
namespace Naxero\AdvancedInstantPurchase\Controller\Ajax;

/**
 * Confirmation Class.
 */
class Confirmation extends \Magento\Framework\App\Action\Action
{
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
     * BillingAddress constructor.
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper
    ) {
        parent::__construct($context);
        $this->customerHelper = $customerHelper;
        $this->customerSession = $customerSession;
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
        if ($this->getRequest()->isAjax()) {
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
            $fn =  'new' . ucfirst($action) . 'Block';
            if (method_exists($this, $fn)) {
                return $this->$fn();
            }
        }

        return $html;
    }

    /**
     * Generates the confirmation block.
     */
    public function newConfirmationBlock()
    {
        return $this->pageFactory->create()->getLayout()
            ->createBlock('Naxero\AdvancedInstantPurchase\Block\Confirmation\Display')
            ->setTemplate('Naxero_AdvancedInstantPurchase::confirmation-data.phtml')
            ->toHtml();
    }

    /**
     * Generates the new address block.
     */
    public function newAddressBlock()
    {
        // Load the customer instance
        $this->customerHelper->loadCustomerData();

        return $this->pageFactory->create()->getLayout()
            ->createBlock(
                'Magento\Customer\Block\Address\Edit',
                'customer_address_edit',
                [
                    'customerSession' => $this->customerSession,
                    'currentCustomer' => $this->currentCustomer
                ]
            )
            ->setTemplate('Magento_Customer::address/edit.phtml')
            ->toHtml();
    }

}
