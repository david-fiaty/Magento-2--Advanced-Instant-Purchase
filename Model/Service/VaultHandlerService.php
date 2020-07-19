<?php
namespace Naxero\AdvancedInstantPurchase\Model\Service;

use Magento\Vault\Api\Data\PaymentTokenInterface;

/**
 * Class VaultHandlerService.
 */
class  VaultHandlerService
{
    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var VaultToken
     */
    public $vaultToken;

    /**
     * @var Config
     */
    public $config;

    /**
     * @var PaymentTokenRepositoryInterface
     */
    public $paymentTokenRepository;

    /**
     * @var PaymentTokenManagementInterface
     */
    public $paymentTokenManagement;

    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @var ManagerInterface
     */
    public $messageManager;

    /**
     * @var ApiHandlerService
     */
    public $apiHandlerService;

    /**
     * @var CardHandlerService
     */
    public $cardHandler;

    /**
     * @var string
     */
    public $customerEmail;

    /**
     * @var int
     */
    public $customerId;

    /**
     * @var string
     */
    public $cardToken;

    /**
     * @var array
     */
    public $cardData = [];

    /**
     * @var array
     */
    public $response = [];

    /**
     * VaultHandlerService constructor.
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \CheckoutCom\Magento2\Model\Vault\VaultToken $vaultToken,
        \Magento\Vault\Api\PaymentTokenRepositoryInterface $paymentTokenRepository,
        \Magento\Vault\Api\PaymentTokenManagementInterface $paymentTokenManagement,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \CheckoutCom\Magento2\Model\Service\ApiHandlerService $apiHandler,
        \CheckoutCom\Magento2\Model\Service\CardHandlerService $cardHandler,
        \CheckoutCom\Magento2\Gateway\Config\Config $config
    ) {
        $this->storeManager = $storeManager;
        $this->vaultToken = $vaultToken;
        $this->paymentTokenRepository = $paymentTokenRepository;
        $this->paymentTokenManagement = $paymentTokenManagement;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->apiHandler = $apiHandler;
        $this->cardHandler = $cardHandler;
        $this->config = $config;
    }

    /**
     * Returns the payment token instance if exists.
     *
     * @param  PaymentTokenInterface $paymentToken
     * @return PaymentTokenInterface|null
     */
    private function foundExistedPaymentToken(PaymentTokenInterface $paymentToken)
    {
        return $this->paymentTokenManagement->getByPublicHash(
            $paymentToken->getPublicHash(),
            $paymentToken->getCustomerId()
        );
    }

    /**
     * Checks if a user has saved cards.
     */
    public function userHasCards($customerId = null)
    {
        // Get the card list
        $cardList = $this->getUserCards($customerId);

        // Check if the user has cards
        if (!empty($cardList)) {
            return  true;
        }

        return false;
    }

    /**
     * Get a user's saved card from public hash.
     */
    public function getCardFromHash($publicHash, $customerId = null)
    {
        if ($publicHash) {
            $cardList = $this->getUserCards($customerId);
            foreach ($cardList as $card) {
                if ($card->getPublicHash() == $publicHash) {
                    return $card;
                }
            }
        }

        return null;
    }

    /**
     * Get a user's last saved card.
     */
    public function getLastSavedCard()
    {
        // Get the cards list
        $cardList = $this->getUserCards();
        if (!empty($cardList)) {
            // Sort the array by date
            usort(
                $cardList,
                function ($a, $b) {
                    return strtotime($a->getCreatedAt()) - strtotime($b->getCreatedAt());
                }
            );

            // Return the most recent
            return $cardList[0];
        }

        return [];
    }

    /**
     * Get a user's saved cards.
     */
    public function getUserCards($customerId = null)
    {
        // Output array
        $output = [];

        // Get the customer id (currently logged in user)
        $customerId = ($customerId) ? $customerId
        : $this->customerSession->getCustomer()->getId();

        // Find the customer cards
        if ((int) $customerId > 0) {
            $cards = $this->paymentTokenManagement->getListByCustomerId($customerId);
            foreach ($cards as $card) {
                if ($this->cardHandler->isCardActive($card)) {
                    $output[] = $card;
                }
            }
        }

        return $output;
    }

    /**
     * Render a payment token.
     */
    public function renderTokenData(PaymentTokenInterface $paymentToken)
    {
        // Get the card details
        $details = json_decode($paymentToken->getTokenDetails() ?: '{}', true);

        // Return the formatted token
        return sprintf(
            '%s, %s: %s, %s: %s',
            $this->cardHandler->getCardScheme($details['type']),
            __('ending'),
            $details['maskedCC'],
            __('expires'),
            $details['expirationDate']
        );
    }
}
