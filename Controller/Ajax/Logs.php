<?php
namespace Naxero\BuyNow\Controller\Ajax;

use Naxero\BuyNow\Model\Config\Naming;

/**
 * Logs controller class
 */
class Logs extends \Magento\Framework\App\Action\Action
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
     * Block
     */
    public $blockHelper;

    /**
     * Logger
     */
    public $loggerHelper;

    /**
     * Tools
     */
    public $toolsHelper;

    /**
     * Logs controller class constructor
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Naxero\BuyNow\Helper\Block $blockHelper,
        \Naxero\BuyNow\Helper\Logger $loggerHelper,
        \Naxero\BuyNow\Helper\Tools $toolsHelper
    ) {
        parent::__construct($context);

        $this->formKeyValidator = $formKeyValidator;
        $this->pageFactory = $pageFactory;
        $this->jsonFactory = $jsonFactory;
        $this->blockHelper = $blockHelper;
        $this->loggerHelper = $loggerHelper;
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
        $html = '';

        // Process the request
        $request = $this->getRequest();
        if ($request->isAjax() && $this->formKeyValidator->validate($request)) {
            $html .= $this->renderDataTree();
        }

        return $this->jsonFactory->create()->setData(
            ['html' => $html]
        );
    }

    /**
     * Renders a browsable data tree.
     */
    public function renderDataTree()
    {
        // Get the product id
        $productId = $this->getRequest()->getParam('product_id');

        // Render the block
        $blockHtml = $this->pageFactory->create()->getLayout()
            ->createBlock('Magento\Framework\View\Element\Template')
            ->setTemplate(Naming::getModuleName() . '::messages/logger.phtml')
            ->setData('config', $this->blockHelper->getConfig($productId))
            ->setData('title', Naming::getModuleTitle())
            ->setData('form_key', $this->toolsHelper->getFormKey())
            ->toHtml();

        return $blockHtml;
    }
}
