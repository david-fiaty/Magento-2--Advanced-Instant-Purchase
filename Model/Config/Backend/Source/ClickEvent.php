<?php
namespace Naxero\AdvancedInstantPurchase\Model\Config\Backend\Source;

/**
 * Class ClickEvent
 */
class ClickEvent implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'popup',
                'label' => __('Open a popup')
            ],
            [
                'value' => 'redirect',
                'label' => __('Redirect to the login page')
            ],
        ];
    }
}
