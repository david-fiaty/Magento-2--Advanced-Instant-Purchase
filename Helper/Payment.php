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
    protected $paymentHelper;
    
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
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Payment\Model\Config $paymentConfig
    ) {
        $this->orderPayment = $orderPayment;
        $this->paymentHelper = $paymentHelper;
        $this->paymentConfig = $paymentConfig;
    }
    
    /**
     * Get all payment methods
     * 
     * @return array
     */ 
    public function getAllPaymentMethods() 
    {
        return $this->paymentHelper->getPaymentMethods();
    }
    
    /**
     * Get key-value pair of all payment methods
     * key = method code & value = method name
     * 
     * @return array
     */ 
    public function getAllPaymentMethodsList() 
    {
        return $this->paymentHelper->getPaymentMethodList();
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
     * Get payment methods that have been used for orders
     * 
     * @return array
     */ 
    public function getUsedPaymentMethods() 
    {
        $collection = $this->_orderPayment;
        $collection->getSelect()->group('method');
        $paymentMethods[] = array('value' => '', 'label' => 'Any');
        foreach ($collection as $col) { 
            $paymentMethods[] = array('value' => $col->getMethod(), 'label' => $col->getAdditionalInformation()['method_title']);            
        }        
        return $paymentMethods;
    }
}