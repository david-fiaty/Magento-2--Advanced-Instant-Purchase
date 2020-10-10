<?php
namespace Naxero\AdvancedInstantPurchase\Controller\Ajax;

use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Instant Purchase order placement.
 */
class Order extends \Magento\Framework\App\Action\Action
{
    /**
     * List of request params handled by the controller.
     *
     * @var array
     */
    private static $knownRequestParams = [
        'form_key',
        'product',
        'instant_purchase_payment_token',
        'instant_purchase_method_code',
        'instant_purchase_shipping_address',
        'instant_purchase_billing_address',
    ];

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var Validator
     */
    private $formKeyValidator;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var QuoteFactory
     */
    public $quoteFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    public $customerRepository;

    /**
     * @var QuoteManagement
     */
    public $quoteManagement;

    /**
     * Class Order constructor 
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Quote\Model\QuoteManagement $quoteManagement
    ) {
        parent::__construct($context);

        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->formKeyValidator = $formKeyValidator;
        $this->productRepository = $productRepository;
        $this->quoteManagement = $quoteManagement;
        $this->quoteFactory = $quoteFactory;
        $this->customerRepository  = $customerRepository;
        $this->quoteManagement = $quoteManagement;
    }

    /**
     * Place an order for a customer.
     *
     * @return JsonResult
     */
    public function execute()
    {
        // Validate the request
        $request = $this->getRequest();
        if (!$this->doesRequestContainAllKnowParams($request)) {
            return $this->createResponse($this->createGenericErrorMessage(), false);
        }
        if (!$this->formKeyValidator->validate($request)) {
            return $this->createResponse($this->createGenericErrorMessage(), false);
        }

        // Prepare the payment data
        $paymentData = [
            'paymentTokenPublicHash' => (string) $request->getParam('instant_purchase_payment_token'),
            'paymentMethodCode' => (string) $request->getParam('instant_purchase_method_code'),
            'shippingAddressId' => (int) $request->getParam('instant_purchase_shipping_address'),
            'billingAddressId' => (int) $request->getParam('instant_purchase_billing_address'),
            'carrierCode' => (string) $request->getParam('instant_purchase_carrier'),
            'shippingMethodCode' => (string) $request->getParam('instant_purchase_shipping'),
            'productId' => (int) $request->getParam('product'),
            'productRequest' => $this->getRequestUnknownParams($request)
        ];

        try {
            // Load the required elements
            $store = $this->storeManager->getStore();
            //$shippingAddress = $this->customerHelper->getShippingAddress($paymentData['shippingAddressId']);
            //$billingAddress = $this->customerHelper->getBillingAddress($paymentData['billingAddressId']);

            // Load the product
            $product = $this->productRepository->getById(
                $paymentData['productId'],
                false,
                $store->getId(),
                false
            );

            // Prepare the quote
            $quote = $this->createQuote();

            // Fill the quote
            /*
            $quote = $this->quoteFilling->fillQuote(
                $quote,
                $product,
                $productRequest
            );
            */

            // Set the payment method
            /*
            $payment = $quote->getPayment();
            $payment->setMethod($paymentData['paymentMethodCode']);
            $payment->save();
            $quote->save();
            */

            $order = $this->createOrder($quote);

        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $this->createResponse($this->createGenericErrorMessage(), false);
        } catch (\Exception $e) {
            return $this->createResponse(
                $e instanceof Magento\Framework\Exception\LocalizedException ? $e->getMessage() : $this->createGenericErrorMessage(),
                false
            );
        }

        // Order confirmation
        $message = __('Your order number is: %1.', 'xxxx1');

        return $this->createResponse($message, true);
    }

    /**
     * Create a new order
     */
    public function createOrder($quote) {
        $order = $this->quoteManagement->submit($quote);
        return $order;
    }

    /**
     * Create a new quote
     */
    public function createQuote($currency = null)
    {
        // Create the quote instance
        $quote = $this->quoteFactory->create();
        $quote->setStore($this->storeManager->getStore());

        // Set the currency
        if ($currency) {
            $quote->setCurrency($currency);
        } else {
            $quote->setCurrency();
        }

        // Set the quote customer
        $customerId = $this->customerSession->getCustomer()->getId();
        $customer =  $this->customerRepository->getById($customerId);
        $quote->assignCustomer($customer);

        return $quote;
    }

    /**
     * Creates error message without exposing error details.
     *
     * @return string
     */
    private function createGenericErrorMessage(): string
    {
        return (string)__('Something went wrong while processing your order. Please try again later.');
    }

    /**
     * Checks if all parameters that should be handled are passed.
     *
     * @param RequestInterface $request
     * @return bool
     */
    private function doesRequestContainAllKnowParams(RequestInterface $request): bool
    {
        foreach (self::$knownRequestParams as $knownRequestParam) {
            if ($request->getParam($knownRequestParam) === null) {
                return false;
            }
        }
        return true;
    }

    /**
     * Filters out parameters that handled by controller.
     *
     * @param RequestInterface $request
     * @return array
     */
    private function getRequestUnknownParams(RequestInterface $request): array
    {
        $requestParams = $request->getParams();
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
     * @param string $message
     * @param bool $successMessage
     * @return JsonResult
     */
    private function createResponse(string $message, bool $successMessage): JsonResult
    {
        /** @var JsonResult $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result->setData([
            'response' => $message
        ]);
        if ($successMessage) {
            $this->messageManager->addSuccessMessage($message);
        } else {
            $this->messageManager->addErrorMessage($message);
        }

        return $result;
    }
}
