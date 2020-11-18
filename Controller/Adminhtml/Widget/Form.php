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

namespace Naxero\BuyNow\Controller\Adminhtml\Widget\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Naxero\BuyNow\Model\Config\Naming;

class Form extends \Magento\Backend\App\Action
{
    /**
     * @var JsonFactory
     */
    public $resultJsonFactory;

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
        \Naxero\BuyNow\Helper\Product $productHelper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productHelper = $productHelper;

        parent::__construct($context);
    }

    /**
     * Get products from category.
     */
    public function getProducts()
    {
        // Prepare the output
        $output = '';

        // Get the file id
        $categoryId = $this->getRequest()->getParam('category_id');

        // Load the requested item
        if ((int) $categoryId > 0) {
            $output .= $this->productHelper->getProducts($categoryId);
        }

        // Return the response
        return $this->resultJsonFactory->create()->setData($output);
    }
}
