<?php
/**
 * Naxero.com
 * Professional ecommerce integrations for Magento.
 *
 * PHP version 7
 *
 * @category  Magento2
 * @package   Naxero
 * @author    Platforms Development Team <contact@naxero.com>
 * @copyright © Naxero.com all rights reserved
 * @license   https://opensource.org/licenses/mit-license.html MIT License
 * @link      https://www.naxero.com
 */

namespace Naxero\BuyNow\Model\Config\Backend\Source;

/**
 * Class PaymentMethods
 */
class PaymentMethods implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var Payment
     */
    public $paymentHelper;

    /**
     * PaymentMethods class constructor.
     */
    public function __construct(
        \Naxero\BuyNow\Helper\Payment $paymentHelper
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
                if ($method->canUseCheckout() && $method->isActive()) {
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
