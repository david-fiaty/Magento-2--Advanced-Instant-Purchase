<?php
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
    protected $orderPayment;
    
    /**
     * Payment Helper Data
     *
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentDataHelper;
    
    /**
     * Payment Model Config
     *
     * @var \Magento\Payment\Model\Config
     */
    protected $paymentConfig;

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
     * Get non card payment methods available
     *
     * @return array
     */
    public function getOtherPaymentMethods()
    {
        // Get the other payment methods
        $options = [];
        $methods = $this->getActivePaymentMethods();
        $allowed = explode(
            ',',
            $this->configHelper->value('payment_methods/other_allowed')
        );
        if (!empty($methods)) {
            foreach ($methods as $method) {
                $code = $method->getCode();
                $canDisplay = $method->canUseCheckout()
                && $method->isActive()
                && in_array($code, $allowed)
                && $code != 'free';
                if ($canDisplay) {
                    $options[] = [
                        'value' => $method->getCode(),
                        'label' => __($method->getTitle())
                    ];
                }
            }
        }

        return $options;
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
