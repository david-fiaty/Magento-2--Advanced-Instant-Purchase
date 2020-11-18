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

namespace Naxero\BuyNow\Block\Adminhtml\Widget\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Naxero\BuyNow\Model\Config\Naming;

/**
 * ProductSelector class.
 */
class ProductSelector extends \Magento\Backend\Block\Template
{
    /**
     * @var ProductList
     */
    public $productListSource;

    /**
     * @var Category
     */
    public $categoryHelper;

    /**
     * GroupSeparator class constructor.
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Naxero\BuyNow\Model\Config\Backend\Source\ProductList $productListSource,
        \Naxero\BuyNow\Helper\Category $categoryHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
     
        $this->productListSource = $productListSource;
        $this->categoryHelper = $categoryHelper;
    }

    /**
     * Render a widget field group separator.
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        // Prepare the separator HTML
        $blockHtml = $this->getLayout()->createBlock('Magento\Backend\Block\Template')
            ->setTemplate(Naming::getModuleName() . '::widget/form/product-selector.phtml')
            ->setData('element_id', $element->getId())
            ->setData('element_name', $element->getName())
            ->setData('element_value', $element->getValue())
            ->setData('element_label', $element->getLabelHtml())
            ->setData('products', $this->productListSource->toOptionArray())
            ->setData('categories', $this->categoryHelper->getCategories())
            ->toHtml();

        // Render the HTML
        $element->setData('after_element_html', $blockHtml);

        return $element;
    }
}
