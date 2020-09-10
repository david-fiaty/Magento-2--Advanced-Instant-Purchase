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
                'label' => __('Open a login popup')
            ],
            [
                'value' => 'redirect',
                'label' => __('Redirect to the login page')
            ],
            [
                'value' => 'continue',
                'label' => __('Open the Instant Purchase popup')
            ]
        ];
    }
}
