<?php
namespace Naxero\AdvancedInstantPurchase\Model\InstantPurchase;

/**
 * Class AvailabilityChecker
 */
class AvailabilityChecker
{
    /**
     * @var CustomerSession
     */
    public $customerSession;

    /**
     * @var Config
     */
    public $config;

    /**
     * @var InstantPurchaseInterface
     */
    public $instantPurchase;

    /**
     * @var VaultHandlerService
     */
    public $vaultHandler;

    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * AvailabilityChecker constructor
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Naxero\AdvancedInstantPurchase\Helper\Config $config,
        \Magento\InstantPurchase\Model\InstantPurchaseInterface $instantPurchase,
        \Naxero\AdvancedInstantPurchase\Model\Service\VaultHandlerService $vaultHandler,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->customerSession = $customerSession;
        $this->config = $config;
        $this->instantPurchase = $instantPurchase;
        $this->vaultHandler = $vaultHandler;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function isAvailable()
    {        
        if ($this->config->value('general/enabled') && $this->config->isCoreInstantPurchaseEnabled()) {
            // Logged in button display
            if ($this->customerSession->isLoggedIn()) {
                // Load the option
                $instantPurchaseOption = $this->instantPurchase->getOption(
                    $this->storeManager->getStore(),
                    $this->customerSession->getCustomer()
                );

                // Test the availability
                return $this->shippingValid($instantPurchaseOption)
                && $this->billingValid($instantPurchaseOption)
                && $this->paymentValid($instantPurchaseOption);
            }
            else if ($this->config->value('guest/show_guest_button')) {
                return true;
            }     

            return false;
        }

        return false;
    }

    /**
     * Check if shipping is valid for display.
     */
    public function shippingValid($instantPurchaseOption)
    {
        if ($this->config->value('registered/bypass_missing_shipping')) {
            return true;
        }

        return $instantPurchaseOption->getShippingAddress()
        && $instantPurchaseOption->getShippingMethod();
    }

    /**
     * Check if billing is valid for display.
     */
    public function billingValid($instantPurchaseOption)
    {
        if ($this->config->value('registered/bypass_missing_billing')) {
            return true;
        }

        return $instantPurchaseOption->getBillingAddress();
    }

    /**
     * Check if payment is valid for display.
     */
    public function paymentValid($instantPurchaseOption)
    {
        if ($this->config->value('registered/bypass_missing_payment')) {
            return true;
        }

        return $this->vaultHandler->getLastSavedCard();
    }
}