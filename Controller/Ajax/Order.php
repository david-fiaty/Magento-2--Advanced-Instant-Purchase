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
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    public $customerRepository;

    /**
     * @var QuoteCreation
     */
    private $quoteCreation;

    /**
     * @var QuoteFilling
     */
    private $quoteFilling;

    /**
     * @var UrlInterface
     */
    public $urlBuilder;

    /**
     * @var Customer
     */
    public $customerHelper;

    /**
     * @var PaymentHandler
     */
    public $paymentHandler;

    /**
     * Class Order constructor 
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\InstantPurchase\Model\QuoteManagement\QuoteCreation $quoteCreation,
        \Magento\InstantPurchase\Model\QuoteManagement\QuoteFilling $quoteFilling,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper,
        \Naxero\AdvancedInstantPurchase\Model\Payment\PaymentHandler $paymentHandler
    ) {
        parent::__construct($context);

        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->formKeyValidator = $formKeyValidator;
        $this->quoteRepository = $quoteRepository;
        $this->productRepository = $productRepository;
        $this->customerRepository  = $customerRepository;
        $this->quoteCreation = $quoteCreation;
        $this->quoteFilling = $quoteFilling;
        $this->urlBuilder = $urlBuilder;
        $this->customerHelper = $customerHelper;
        $this->paymentHandler = $paymentHandler;
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
        if (!$this->formKeyValidator->validate($params)) {
            return $this->createResponse($this->createGenericErrorMessage(), false);
        }

        // Validate the request parameters
        $request = $this->getRequestData($params);
        if (!$this->doesRequestContainAllKnowParams($request)) {
            return $this->createResponse($this->createGenericErrorMessage(), false);
        }


        // Prepare the payment data
        $paymentData = [
            'paymentTokenPublicHash' => (string) $request['instant_purchase_payment_token'],
            'paymentMethodCode' => (string) $request['instant_purchase_method_code'],
            'shippingAddressId' => (int) $request['instant_purchase_shipping_address'],
            'billingAddressId' => (int) $request['instant_purchase_billing_address'],
            'carrierCode' => (string) $request['instant_purchase_carrier'],
            'shippingMethodCode' => (string) $request['instant_purchase_shipping'],
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
                $payment = $quote->getPayment();
                $payment->setQuote($quote);
                $payment->setMethod($paymentData['paymentMethodCode']);
                $payment->importData([
                    'method' => $paymentData['paymentMethodCode']
                ]);
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
                }
                else {
                    return $this->createResponse($this->createGenericErrorMessage(), false);
                }
            }
        } 
        catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $this->createResponse($e->getMessage(), false);

            return $this->createResponse($this->createGenericErrorMessage(), false);
        } 
        catch (\Exception $e) {
            return $this->createResponse($e->getMessage(), false);

            return $this->createResponse(
                $e instanceof Magento\Framework\Exception\LocalizedException ? $e->getMessage() : $this->createGenericErrorMessage(),
                false
            );
        }
    }

    /* Get the request data.
     *
     * @return string
     */
    public function getRequestData($request)
    {
        $params = $request->getParams();
        $formatted = array_merge($params['aip'], array());
        unset($params['aip']);

        return array_merge($params, $formatted[0]);
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
    private function doesRequestContainAllKnowParams(array $request): bool
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
     * @param RequestInterface $request
     * @return array
     */
    private function getRequestUnknownParams(array $requestParams): array
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
            $this->messageManager->addComplexSuccessMessage(
                'naxeroAipOrderSuccessMessage', 
                ['message' => $message],
                null
            );
        } else {
            $this->messageManager->addErrorMessage($message);
        }

        return $result;
    }
}
