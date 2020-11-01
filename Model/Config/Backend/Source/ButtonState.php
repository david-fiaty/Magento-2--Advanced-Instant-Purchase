<?php
namespace Naxero\AdvancedInstantPurchase\Model\Config\Backend\Source;

/**
 * Class ButtonState
 */
class ButtonState implements \Magento\Framework\Option\ArrayInterface
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
                'value' => 1,
                'label' => __('Disabled')
            ],
            [
                'value' => 0,
                'label' => __('Enabled')
            ]
        ];
    }
}
