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

namespace Naxero\BuyNow\Controller\Logs;

use Naxero\BuyNow\Model\Config\Naming;

/**
 * Index controller class
 */
class Index extends \Magento\Framework\App\Action\Action
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
     * Index controller class constructor
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Naxero\BuyNow\Helper\Block $blockHelper,
        \Naxero\BuyNow\Helper\Logger $loggerHelper
    ) {
        parent::__construct($context);

        $this->formKeyValidator = $formKeyValidator;
        $this->pageFactory = $pageFactory;
        $this->jsonFactory = $jsonFactory;
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
            ->createBlock(Naming::getModulePath() . '\Block\Debug\UiLogger')
            ->setTemplate(Naming::getModuleName() . '::messages/ui-logger.phtml')
            ->setData('product_id', $productId)
            ->setData('title', Naming::getModuleTitle())
            ->toHtml();

        return $blockHtml;
    }
}
