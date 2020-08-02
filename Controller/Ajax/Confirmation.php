<?php
namespace Naxero\AdvancedInstantPurchase\Controller\Ajax;

/**
 * Confirmation Class.
 */
class Confirmation extends \Magento\Framework\App\Action\Action
{
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
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper
    ) {
        parent::__construct($context);
        $this->customerHelper = $customerHelper;
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
            // Get the list of addresses
            $items = $this->customerHelper->getConfirmContent();

            // Load the blocks HTML
            $html .= $this->loadBlock();
        }

        return $this->jsonFactory->create()->setData(
            ['html' => $html]
        );
    }

    /**
     * Generate a block.
     */
    public function loadBlock()
    {
        return $this->pageFactory->create()->getLayout()
            ->createBlock('Naxero\AdvancedInstantPurchase\Block\Confirmation\Display')
            ->setTemplate('Naxero_AdvancedInstantPurchase::confirmation-data.phtml')
            ->toHtml();
    }
}
