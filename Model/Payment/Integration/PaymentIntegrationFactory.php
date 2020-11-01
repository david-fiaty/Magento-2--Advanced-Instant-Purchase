<?php
namespace Naxero\AdvancedInstantPurchase\Model\Payment\Integration;

class PaymentIntegrationFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create config model
     *
     * @param  string|\Magento\Framework\Simplexml\Element $sourceData
     * @return \Magento\Framework\App\Config\Base
     */
    public function create($classPath)
    {
        return $this->objectManager->create($classPath);
    }
}
