<?php
namespace Naxero\AdvancedInstantPurchase\Controller\Ajax;

use Naxero\AdvancedInstantPurchase\Model\Config\Naming;

/**
 * Logs class.
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
     * Logger
     */
    public $loggerHelper;

    /**
     * Class Logs constructor 
     */
    public function __construct(
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Naxero\AdvancedInstantPurchase\Helper\Logger $loggerHelper
    ) {
        parent::__construct($context);

        $this->formKeyValidator = $formKeyValidator;
        $this->pageFactory = $pageFactory;
        $this->jsonFactory = $jsonFactory;
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
            $html .= $this->loggerHelper->renderDataTree($data, $config);
        }

        return $this->jsonFactory->create()->setData(
            ['html' => $html]
        );
    }

    /**
     * Renders a browsable data tree.
     */
    public function renderDataTree($data, $config) {
        return $this->pageFactory->create()->getLayout()
        ->createBlock('Magento\Framework\View\Element\Template')
        ->setTemplate(Naming::getModuleName() . '::messages/ui-logger.phtml')
        ->setData('data', $data)
        ->setData('config', $config)
        ->setData('title', Naming::getModuleTitle())
        ->toHtml();  
    }
}
