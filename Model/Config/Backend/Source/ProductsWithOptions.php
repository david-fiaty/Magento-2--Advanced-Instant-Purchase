<?php
namespace Naxero\AdvancedInstantPurchase\Model\Config\Backend\Source;

/**
 * Class ProductsWithOptions
 */
class ProductsWithOptions implements \Magento\Framework\Option\ArrayInterface
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
                'value' => 'open',
                'label' => __('Open the popup with options to select')
            ],
            [
                'value' => 'validate',
                'label' => __('Force the user to select options')
            ],
            [
                'value' => 'redirect',
                'label' => __('Redirect the user to the product view page')
            ]
        ];
    }
}
