<?php
namespace Naxero\AdvancedInstantPurchase\Model\Service;

use Magento\Vault\Api\Data\PaymentTokenInterface;

/**
 * Class VaultHandlerService.
 */
class VaultHandlerService
{
    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

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
     * @var CardHandlerService
     */
    public $cardHandler;

    /**
     * @var Config
     */
    public $configHelper;

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
        \Magento\Vault\Api\PaymentTokenRepositoryInterface $paymentTokenRepository,
        \Magento\Vault\Api\PaymentTokenManagementInterface $paymentTokenManagement,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Naxero\AdvancedInstantPurchase\Model\Service\CardHandlerService $cardHandler,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper
    ) {
        $this->storeManager = $storeManager;
        $this->paymentTokenRepository = $paymentTokenRepository;
        $this->paymentTokenManagement = $paymentTokenManagement;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->cardHandler = $cardHandler;
        $this->configHelper = $configHelper;
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
                    $a = is_array($a) && isset($a['instance']) ? $a['instance'] : $a;
                    $b = is_array($b) && isset($b['instance']) ? $b['instance'] : $b;
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
                    $details = json_decode($card->getTokenDetails());
                    $output[] = [
                        'instance' => $card,
                        'icon' => $this->cardHandler->getCardIcon($details->type),
                        'token' => $this->renderTokenData($card),
                        'method_code' => $card->getPaymentMethodCode()
                    ];
                }
            }
        }

        return $output;
    }

    /**
     * Get allowed cards
     */
    public function getAllowedCards()
    {
        // Prepare the output
        $output = [];

        // Get the user cards list
        $cardList = $this->getUserCards();

        // Get the allowed cards list
        $allowedCards = explode(
            ',',
            $this->configHelper->value('payment_methods/cards_allowed')
        );

        // Filter the user cards list
        if (!empty($cardList) && !empty($allowedCards)) {
            foreach($cardList as $card) {
                if (in_array($card['instance']->getCode(), $allowedCards)) {
                    $output[] = $card;
                }
            }
        }

        return $output;
    }

    /**
     * Format a payment token
     */
    public function formatPaymentToken(PaymentTokenInterface $paymentToken)
    {
        return $this->renderTokenData($paymentToken);
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

    public function preparePaymentToken() {
        // Get the last saved cards
        $card = $this->getLastSavedCard();

        // Summary
        $summary = isset($card['instance'])
        ? $this->formatPaymentToken($card['instance'])
        : '';

        // Public hash
        $publicHash = isset($card['instance'])
        ? $card['instance']->getPublicHash()
        : '';

        $methodCode = isset($card['instance'])
        ? $card['instance']->getPaymentMethodCode()
        : '';
        
        return [
            'publicHash' => $publicHash,
            'summary' => $summary,
            'method_code' => $methodCode
        ];
    }
}
