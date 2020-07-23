<?php
namespace Naxero\AdvancedInstantPurchase\Model\InstantPurchase;

use Magento\Vault\Api\Data\PaymentTokenInterface;

/**
 * Class TokenFormatter
 */
class TokenFormatter
{
    /**
     * @var VaultHandlerService
     */
    public $vaultHandler;

    /**
     * TokenFormatter constructor.
     */
    public function __construct(
        \Naxero\AdvancedInstantPurchase\Model\Service\VaultHandlerService $vaultHandler
    ) {
        $this->vaultHandler = $vaultHandler;
    }

    /**
     * @inheritdoc
     */
    public function formatPaymentToken(PaymentTokenInterface $paymentToken)
    {
        // Return the formatted token
        return $this->vaultHandler->renderTokenData($paymentToken);
    }
}
