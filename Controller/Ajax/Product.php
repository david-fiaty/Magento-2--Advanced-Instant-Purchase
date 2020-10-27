<?php
namespace Naxero\AdvancedInstantPurchase\Controller\Ajax;

use Naxero\AdvancedInstantPurchase\Model\Config\Naming;

/**
 * Product controller class
 */
class Product extends \Magento\Framework\App\Action\Action
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
     * Product controller class constructor
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        
        $this->formKeyValidator = $formKeyValidator;
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
        if ($request->isAjax() && $this->formKeyValidator->validate($request)) {
            //$html .= $this->loadBlock();
        }

        return $this->jsonFactory->create()->setData(
            ['html' => $html]
        );
    }

    /**
     * Get a product box display.
     */
    public function getProductBox()
    {

    }

    /**
     * Get a product options display.
     */
    public function getProductOptions()
    {

    }
}
