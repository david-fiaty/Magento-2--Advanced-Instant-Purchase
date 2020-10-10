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
     * @var PlaceOrderService
     */
    private $placeOrderService;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * Class Order constructor 
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Naxero\AdvancedInstantPurchase\Model\Service\PlaceOrderService $placeOrderService
    ) {
        parent::__construct($context);

        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->formKeyValidator = $formKeyValidator;
        $this->productRepository = $productRepository;
        $this->placeOrderService = $placeOrderService;
        $this->orderRepository = $orderRepository;
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

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/p.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($paymentData, 1));

        try {
            // Load the required elements
            $customer = $this->customerSession->getCustomer();
            $store = $this->storeManager->getStore();

            // Load the product
            $product = $this->productRepository->getById(
                $paymentData['productId'],
                false,
                $store->getId(),
                false
            );

            // Place the order
            $orderId = $this->placeOrderService->placeOrder(
                $store,
                $customer,
                $product,
                $productRequest,
                $paymentData
            );
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $this->createResponse($this->createGenericErrorMessage(), false);
        } catch (\Exception $e) {
            return $this->createResponse(
                $e instanceof Magento\Framework\Exception\LocalizedException ? $e->getMessage() : $this->createGenericErrorMessage(),
                false
            );
        }

        // Order confirmation
        $order = $this->orderRepository->get($orderId);
        $message = __('Your order number is: %1.', $order->getIncrementId());

        return $this->createResponse($message, true);
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
