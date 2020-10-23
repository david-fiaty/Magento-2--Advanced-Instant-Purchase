<?php
namespace Naxero\AdvancedInstantPurchase\Block\Ui;

/**
 * Data Class.
 */
class Autocomplete extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Autocomplete constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Get configuration element
        $html = $element->getElementHtml();


        // add icon on datepicker 
        $html .= '<button type="button" class="ui-datepicker-trigger '
            .'v-middle"><span>Select Date</span></button>';
        // add datepicker with element by jquery
        $html .= '<script type="text/javascript">
            require(["jquery"], function ($) {
                $(document).ready(function () {
                    alert("batman is not ded");
                });
            });
            </script>';
        // return datepicker element
        return $html;
    }
}
