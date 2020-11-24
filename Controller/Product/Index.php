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

namespace Naxero\BuyNow\Controller\Product;

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
        $this->blockHelper = $blockHelper;
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
        // Todo - add form key validatino
        //if ($request->isAjax() && $this->formKeyValidator->validate($request)) {
        if ($request->isAjax()) {
            $html .= $this->renderProductGallery();
        }

        return $this->jsonFactory->create()->setData(
            ['html' => $html]
        );
    }

    /**
     * Renders a product gallery.
     */
    public function renderProductGallery()
    {
        // Get the product id
        $productId = $this->getRequest()->getParam('product_id');

        // Render the block
        $blockHtml = $this->pageFactory->create()->getLayout()
            ->createBlock('Magento\Framework\View\Element\Template')
            ->setTemplate(Naming::getModuleName() . '::product/widget-gallery.phtml')
            ->setData('config', $this->blockHelper->getConfig($productId))
            ->setData('title', Naming::getModuleTitle())
            ->toHtml();

        return $blockHtml;
    }
}
