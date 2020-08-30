<?php
namespace Naxero\AdvancedInstantPurchase\Model\Config\Backend\Source;

/**
 * Class BillingAddressesOrder
 */
class BillingAddressesOrder implements \Magento\Framework\Option\ArrayInterface
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
                'value' => 'most_used',
                'label' => __('Most used')
            ],
            [
                'value' => 'last_used',
                'label' => __('Last used')
            ],
            [
                'value' => 'first_used',
                'label' => __('First used')
            ],
            [
                'value' => 'closest',
                'label' => __('Closest')
            ],
        ];
    }
}
