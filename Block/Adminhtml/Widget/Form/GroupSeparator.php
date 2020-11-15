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
     * Render a widget field group separator.
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        return $element->setData(
            'after_element_html', 
            $this->getData('text')
        );
    }
}
