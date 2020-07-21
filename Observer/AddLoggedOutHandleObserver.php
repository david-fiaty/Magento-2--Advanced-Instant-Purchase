<?php
namespace Naxero\AdvancedInstantPurchase\Observer;

/**
 * Class AddLoggedOutHandleObserver.
 */
class AddLoggedOutHandleObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var Session
     */
    public $customerSession;

    /**
     * AddLoggedOutHandleObservers constructor.
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->customerSession = $customerSession;
    }

    /**
     * Add a custom handle responsible for adding the trigger-ajax-login class
     *
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $layout = $observer->getEvent()->getLayout();
 
        if (!$this->customerSession->isLoggedIn()) {
            $layout->getUpdate()->addHandle('ajaxlogin_customer_logged_out');
        }
    }
}
