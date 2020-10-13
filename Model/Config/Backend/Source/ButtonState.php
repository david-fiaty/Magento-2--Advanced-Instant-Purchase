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
                'value' => true,
                'label' => __('Button disabled by default')
            ],
            [
                'value' => false,
                'label' => __('Button enabled by default')
            ]
        ];
    }
}
