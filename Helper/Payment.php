<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

/**
 * Class Product
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
     * Payment Helper constructor.
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Payment\Collection $orderPayment,
        \Magento\Payment\Helper\Data $paymentDataHelper,
        \Magento\Payment\Model\Config $paymentConfig
    ) {
        $this->orderPayment = $orderPayment;
        $this->paymentDataHelper = $paymentDataHelper;
        $this->paymentConfig = $paymentConfig;
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
        $options = [];
        $methods = $this->getActivePaymentMethods();
        if (!empty($methods)) {
            foreach ($methods as $method) {
                if ($method->canUseCheckout() && $method->isActive() && !$method->isGateway()) {
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