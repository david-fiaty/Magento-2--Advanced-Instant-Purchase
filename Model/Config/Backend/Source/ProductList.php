<?php
namespace Naxero\AdvancedInstantPurchase\Model\Config\Backend\Source;

/**
 * Class PaymentMethods
 */
class ProductList implements \Magento\Framework\Option\ArrayInterface
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
                'label' => __('A')
            ],
            [
                'value' => 0,
                'label' => __('B')
            ]
        ];
    }
}
