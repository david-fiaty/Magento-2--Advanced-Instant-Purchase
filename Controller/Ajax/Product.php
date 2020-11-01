<?php
namespace Naxero\BuyNow\Controller\Ajax;

use Naxero\BuyNow\Model\Config\Naming;

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
     * @var Block
     */
    public $blockHelper;

    /**
     * @var Purchase
     */
    public $purchaseHelper;

    /**
     * @var Logger
     */
    public $loggerHelper;

    /**
     * Logs controller class constructor
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Naxero\BuyNow\Helper\Block $blockHelper,
        \Naxero\BuyNow\Helper\Purchase $purchaseHelper,
        \Naxero\BuyNow\Helper\Logger $loggerHelper
    ) {
        parent::__construct($context);

        $this->formKeyValidator = $formKeyValidator;
        $this->pageFactory = $pageFactory;
        $this->jsonFactory = $jsonFactory;
        $this->blockHelper = $blockHelper;
        $this->purchaseHelper = $purchaseHelper;
        $this->loggerHelper = $loggerHelper;
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
        if ($request->isAjax() && $this->formKeyValidator->validate($request)) {
            $productId = $this->getRequest()->getParam('product_id');
            $html .= $this->purchaseHelper->renderProductBox($productId);
        }

        return $this->jsonFactory->create()->setData(
            ['html' => $html]
        );
    }
}
