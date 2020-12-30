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

namespace Naxero\BuyNow\Controller\Adminhtml\Widget;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Naxero\BuyNow\Model\Config\Naming;

class Form extends \Magento\Backend\App\Action
{
    /**
     * @var JsonFactory
     */
    public $resultJsonFactory;

    /**
     * @var Validator
     */
    public $formKeyValidator;

    /**
     * @var PageFactory
     */
    public $pageFactory;

    /**
     * @var Product
     */
    public $productHelper;

    /**
     * Form class constructor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Naxero\BuyNow\Helper\Product $productHelper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->pageFactory = $pageFactory;
        $this->productHelper = $productHelper;

        parent::__construct($context);
    }

    /**
     * Get products from category.
     */
    public function execute()
    {
        // Prepare the output
        $html = '';

        // Get the file id
        $categoryId = $this->getRequest()->getParam('category_id');

        // Load the requested item
        if ((int) $categoryId > 0) {
            $html .= $this->renderProductOptions($categoryId);
        }

        // Return the response
        return $this->resultJsonFactory->create()->setData([
            'html' => $html
        ]);
    }

    /**
     * Get the terms and conditions.
     */
    public function renderProductOptions($categoryId)
    {
        return $this->pageFactory->create()->getLayout()
            ->createBlock('Magento\Backend\Block\Template')
            ->setTemplate(Naming::getModuleName() . '::product/attributess.phtml')
            ->setData('products', $this->productHelper->getProducts($categoryId))
            ->toHtml();
    }
}
