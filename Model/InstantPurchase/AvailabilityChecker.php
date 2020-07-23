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
     * @var VaultHandlerService
     */
    private $vaultHandler;

    /**
     * AvailabilityChecker constructor
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Naxero\AdvancedInstantPurchase\Helper\Config $config,
        \Naxero\AdvancedInstantPurchase\Model\Service\VaultHandlerService $vaultHandler
    ) {
        $this->customerSession = $customerSession;
        $this->config = $config;
        $this->vaultHandler = $vaultHandler;
    }

    /**
     * @inheritdoc
     */
    public function isAvailable()
    {
        if ($this->config->value('general/enabled') && $this->config->isCoreInstantPurchaseEnabled()) {
            // Logged in button display
            if ($this->customerSession->isLoggedIn()) {
                return $this->shippingValid()
                && $this->billingValid()
                && $this->paymentValid();
            }        

            // Guest button display
            return $this->config->value('guest/show_guest_button');
        }

        return false;
    }

    /**
     * Check if shipping is valid for display.
     */
    public function shippingValid()
    {
        return $this->config->value('registered/bypass_missing_shipping');
    }

    /**
     * Check if billing is valid for display.
     */
    public function billingValid()
    {
        return $this->config->value('registered/bypass_missing_billing');
    }

    /**
     * Check if payment is valid for display.
     */
    public function paymentValid()
    {
        return $this->config->value('registered/bypass_missing_payment');
    }
}