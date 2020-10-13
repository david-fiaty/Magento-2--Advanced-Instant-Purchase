<?php
namespace Naxero\AdvancedInstantPurchase\Model\Config\Backend\Source;

/**
 * Class PaymentMethods
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
                'label' => __('Button disabled by default')
            ],
            [
                'value' => 0,
                'label' => __('Button enabled by default')
            ]
        ];
    }
}
