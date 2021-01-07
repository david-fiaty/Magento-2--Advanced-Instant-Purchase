<?php

/**
 * Naxero.com
 * Professional ecommerce integrations for Magento.
 *
 * PHP version 7
 *
 * @category  Magento2
 * @package   Naxero
 * @author    Platforms Development Team <contact@naxero.com>
 * @copyright © Naxero.com all rights reserved
 * @license   https://opensource.org/licenses/mit-license.html MIT License
 * @link      https://www.naxero.com
 */

namespace Naxero\BuyNow\Controller\Order;

use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;

/**
 * Order controller class
 */
class Request extends \Magento\Framework\App\Action\Action
{
    /**
     * List of request params handled by the controller.
     *
     * @var array
     */
    public static $knownRequestParams = [
        'form_key',
        'product',
        'payment_token',
        'payment_method_code',
        'shipping_address',
        'billing_address',
    ];

    /**
     * @var IntegrationsManager
     */
    public $integrationsManager;

    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var Validator
     */
    public $formKeyValidator;

    /**
     * @var CartRepositoryInterface
     */
    public $quoteRepository;

    /**
     * @var ProductRepositoryInterface
     */
    public $productRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    public $customerRepository;

    /**
     * @var QuoteCreation
     */
    public $quoteCreation;

    /**
     * @var QuoteFilling
     */
    public $quoteFilling;

    /**
     * @var UrlInterface
     */
    public $urlBuilder;

    /**
     * @var Customer
     */
    public $customerHelper;

    /**
     * @var VaultHandlerService
     */
    public $vaultHandlerService;

    /**
     * Order controller class constructor
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\InstantPurchase\PaymentMethodIntegration\IntegrationsManager $integrationsManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\InstantPurchase\Model\QuoteManagement\QuoteCreation $quoteCreation,
        \Magento\InstantPurchase\Model\QuoteManagement\QuoteFilling $quoteFilling,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Naxero\BuyNow\Helper\Customer $customerHelper,
        \Naxero\BuyNow\Model\Service\VaultHandlerService $vaultHandlerService

    ) {
        parent::__construct($context);

        $this->integrationsManager = $integrationsManager;
        $this->storeManager = $storeManager;
        $this->formKeyValidator = $formKeyValidator;
        $this->quoteRepository = $quoteRepository;
        $this->productRepository = $productRepository;
        $this->customerRepository  = $customerRepository;
        $this->quoteCreation = $quoteCreation;
        $this->quoteFilling = $quoteFilling;
        $this->urlBuilder = $urlBuilder;
        $this->customerHelper = $customerHelper;
        $this->vaultHandlerService = $vaultHandlerService;
    }

    /**
     * Place an order for a customer.
     *
     * @return JsonResult
     */
    public function execute()
    {
        // Validate the form key
        $params = $this->getRequest();
        if (!$this->formKeyValidator->validate($params) || !$params->isAjax()) {
            return $this->createResponse($this->createGenericErrorMessage(), false);
        }

        // Validate the request parameters
        $request = $this->getRequestData($params);
        if (!$this->doesRequestContainAllKnowParams($request)) {
            return $this->createResponse($this->createGenericErrorMessage(), false);
        }
        
        // Prepare the payment data
        $paymentData = [
            'paymentTokenPublicHash' => (string) $request['payment_token'],
            'paymentMethodCode' => (string) $request['payment_method_code'],
            'shippingAddressId' => (int) $request['shipping_address'],
            'billingAddressId' => (int) $request['billing_address'],
            'carrierCode' => (string) $request['carrier_code'],
            'shippingMethodCode' => (string) $request['shipping_method_code'],
            'productId' => (int) $request['product'],
            'productRequest' => $this->getRequestUnknownParams($request)
        ];

        try {
            // Load the required elements
            $store = $this->storeManager->getStore();
            $customer = $this->customerHelper->getCustomer();

            // Get the billing address
            $billingAddress = $customer->getAddressById($paymentData['billingAddressId']);

            // Get the shipping address
            $shippingAddress = $customer->getAddressById($paymentData['shippingAddressId']);
            $shippingAddress->setCollectShippingRates(true);
            $shippingAddress->setShippingMethod($paymentData['carrierCode']);

            // Load the product
            $product = $this->productRepository->getById(
                $paymentData['productId'],
                false,
                $store->getId(),
                false
            );

            // Create the quote
            $quote = $this->quoteCreation->createQuote(
                $store,
                $customer,
                $shippingAddress,
                $billingAddress
            );

            // Set the store
            $quote->setStore($store)->save();

            // Fill the quote
            $quote = $this->quoteFilling->fillQuote(
                $quote,
                $product,
                $paymentData['productRequest']
            );

            // Set the shipping method
            $quote->getShippingAddress()->addData($shippingAddress->getData());

            // Set the payment method
            if ($paymentData['paymentMethodCode'] != 'free') {
                // Payment token
                $paymentToken = $this->vaultHandlerService->getCardFromHash(
                    $paymentData['paymentTokenPublicHash'],
                    $customer->getId()
                );

                // Payment set up
                $payment = $quote->getPayment();
                $payment->setQuote($quote);
                $payment->setMethod($paymentData['paymentMethodCode']);
                $payment->importData(['method' => $paymentData['paymentMethodCode']]);
                $payment->setAdditionalInformation($this->buildPaymentAdditionalInformation(
                    $paymentToken,
                    $quote->getStoreId()
                ));

                $payment->save();
            }

            // Save the quote
            $quote->collectTotals();
            $this->quoteRepository->save($quote);
            $quote = $this->quoteRepository->get($quote->getId());

            // Send the payment request and get the response
            $paymentMethod = $this->paymentHandler->loadMethod($paymentData['paymentMethodCode']);
            $paymentResponse = $paymentMethod->sendRequest($quote, $paymentData);

            // Create the order
            if ($paymentResponse->paymentSuccess()) {
                $order = $paymentResponse->createOrder($quote, $paymentResponse);
                if ($order) {
                    $message = json_encode([
                        'order_url' => $this->urlBuilder->getUrl('sales/order/view/order_id/' . $order->getId()),
                        'order_increment_id' => $order->getIncrementId()
                    ]);

                    return $this->createResponse($message, true);
                } else {
                    return $this->createResponse($this->createGenericErrorMessage(), false);
                }
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $this->createResponse($e->getMessage(), false);

            return $this->createResponse($this->createGenericErrorMessage(), false);
        } catch (\Exception $e) {
            return $this->createResponse($e->getMessage(), false);

            return $this->createResponse(
                $e instanceof Magento\Framework\Exception\LocalizedException ? $e->getMessage() : $this->createGenericErrorMessage(),
                false
            );
        }
    }

    /**
     * Builds payment additional information based on token.
     *
     * @param PaymentTokenInterface $paymentToken
     * @param int $storeId
     * @return array
     */
    public function buildPaymentAdditionalInformation(PaymentTokenInterface $paymentToken, int $storeId): array
    {
        $common = [
            PaymentTokenInterface::CUSTOMER_ID => $paymentToken->getCustomerId(),
            PaymentTokenInterface::PUBLIC_HASH => $paymentToken->getPublicHash(),
            VaultConfigProvider::IS_ACTIVE_CODE => true,

            // mark payment
            self::MARKER => 'true',
        ];

        $integration = $this->integrationManager->getByToken($paymentToken, $storeId);
        $specific = $integration->getAdditionalInformation($paymentToken);

        $additionalInformation = array_merge($common, $specific);
        return $additionalInformation;
    }

    /* Get the request data.
     *
     * @return string
     */
    public function getRequestData($request)
    {
        $params = $request->getParams();
        $formatted = array_merge($params['nbn'], []);
        unset($params['nbn']);

        return array_merge($params, $formatted[0]);
    }

    /**
     * Creates error message without exposing error details.
     *
     * @return string
     */
    public function createGenericErrorMessage(): string
    {
        return (string)__('The request could not be processed because of invalid or missing parameters.');
    }

    /**
     * Checks if all parameters that should be handled are passed.
     *
     * @param  RequestInterface $request
     * @return bool
     */
    public function doesRequestContainAllKnowParams(array $request): bool
    {
        foreach (self::$knownRequestParams as $knownRequestParam) {
            if ($request[$knownRequestParam] === null) {
                return false;
            }
        }
        return true;
    }

    /**
     * Filters out parameters that handled by controller.
     *
     * @param  RequestInterface $request
     * @return array
     */
    public function getRequestUnknownParams(array $requestParams): array
    {
        $unknownParams = [];
        foreach ($requestParams as $param => $value) {
            if (!isset(self::$knownRequestParams[$param])) {
                $unknownParams[$param] = $value;
            }
        }
        return $unknownParams;
    }

    /**
     * Creates response with a operation status message.
     *
     * @param  string $message
     * @param  bool   $successMessage
     * @return JsonResult
     */
    public function createResponse(string $message, bool $successMessage): JsonResult
    {
        /**
 * @var JsonResult $result
*/
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result->setData(
            [
            'response' => $message
            ]
        );
        if ($successMessage) {
            $this->messageManager->addComplexSuccessMessage(
                'nbnOrderSuccessMessage',
                ['message' => $message],
                null
            );
        } else {
            $this->messageManager->addErrorMessage($message);
        }

        return $result;
    }
}
