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
 * CategorySelector class.
 */
class CategorySelector extends \Magento\Backend\Block\Template
{
    /**
     * @var Category
     */
    public $categoryHelper;

    /**
     * CategorySelector class constructor.
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Naxero\BuyNow\Helper\Category $categoryHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
     
        $this->categoryHelper = $categoryHelper;
    }

    /**
     * Render the widget field.
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        // Prepare the separator HTML
        $blockHtml = $this->getLayout()->createBlock('Magento\Backend\Block\Template')
            ->setTemplate(Naming::getModuleName() . '::widget/form/category-selector.phtml')
            ->setData('element', $element)
            ->setData('categories', $this->categoryHelper->getCategories())
            ->toHtml();

        // Render the HTML
        $element->setData('after_element_html', $blockHtml);

        return $element;
    }
}