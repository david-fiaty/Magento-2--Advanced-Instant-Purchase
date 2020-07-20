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
            // Guest button display
            if ($this->config->value('display/show_guest_button')) {
                return true;
            }

            // Logged in button display
            if ($this->customerSession->isLoggedIn()) {
                return true;
            }        

            return false;
        }

        return false;
    }
}
