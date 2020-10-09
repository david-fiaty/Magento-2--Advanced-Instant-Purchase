<?php
namespace Naxero\AdvancedInstantPurchase\Model\Config\Backend\Source;

/**
 * Class OtherPaymentMethods
 */
class OtherPaymentMethods implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var Payment
     */
    public $paymentHelper;

    /**
     * OtherPaymentMethods methods class constructor.
     */
    public function __construct(
        \Naxero\AdvancedInstantPurchase\Helper\Payment $paymentHelper
    ) {
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $methods = $this->paymentHelper->getActivePaymentMethods();
        if (!empty($methods)) {
            foreach ($methods as $method) {
                if ($method->canUseCheckout() && $method->isActive() && !$method->isGaateway()) {
                    $options[] = [
                        'value' => $method->getCode(),
                        'label' => __($method->getTitle())
                    ];
                }
            }
        }

        return $options;
    }
}
