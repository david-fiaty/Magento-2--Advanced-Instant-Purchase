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

namespace Naxero\BuyNow\Helper;

/**
 * Class Payment helper.
 */
class Payment extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Order Payment
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\Payment\Collection
     */
    public $orderPayment;

    /**
     * Payment Helper Data
     *
     * @var \Magento\Payment\Helper\Data
     */
    public $paymentDataHelper;

    /**
     * Payment Model Config
     *
     * @var \Magento\Payment\Model\Config
     */
    public $paymentConfig;

    /**
     * @var Config
     */
    public $configHelper;

    /**
     * Payment helper constructor.
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Payment\Collection $orderPayment,
        \Magento\Payment\Helper\Data $paymentDataHelper,
        \Magento\Payment\Model\Config $paymentConfig,
        \Naxero\BuyNow\Helper\Config $configHelper
    ) {
        $this->orderPayment = $orderPayment;
        $this->paymentDataHelper = $paymentDataHelper;
        $this->paymentConfig = $paymentConfig;
        $this->configHelper = $configHelper;
    }

    /**
     * Get all payment methods
     *
     * @return array
     */
    public function getAllPaymentMethods()
    {
        return $this->paymentDataHelper->getPaymentMethods();
    }

    /**
     * Get key-value pair of all payment methods
     * key = method code & value = method name
     *
     * @return array
     */
    public function getAllPaymentMethodsList()
    {
        return $this->paymentDataHelper->getPaymentMethodList();
    }

    /**
     * Get active/enabled payment methods
     *
     * @return array
     */
    public function getActivePaymentMethods()
    {
        return $this->paymentConfig->getActiveMethods();
    }

    /**
     * Check if an item is a saved card.
     */
    public function isSavedCard($item, $savedCards)
    {
        foreach ($savedCards as $card) {
            if ($card['method_code'] == $item['value']) {
                return true;
            }
        }

        return false;
    }
}
