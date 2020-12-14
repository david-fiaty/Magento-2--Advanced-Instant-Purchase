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
 * GroupSeparator class.
 */
class GroupSeparator extends \Magento\Backend\Block\Template
{
    /**
     * GroupSeparator class constructor.
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Render the widget field.
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        // Prepare the separator HTML
        $blockHtml = $this->getLayout()->createBlock('Magento\Backend\Block\Template')
            ->setTemplate(Naming::getModuleName() . '::widget/form/group-separator.phtml')
            ->setData('text', $this->getData('text'))
            ->toHtml();

        // Render the HTML
        return $element->setData('after_element_html', $blockHtml);
    }
}
