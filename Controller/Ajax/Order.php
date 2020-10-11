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
    public static $knownRequestParams = [
        'form_key',
        'product',
        'instant_purchase_payment_token',
        'instant_purchase_method_code',
        'instant_purchase_shipping_address',
        'instant_purchase_billing_address',
    ];

    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @var Validator
     */
    public $formKeyValidator;

    /**
     * @var UrlInterface
     */
    public $urlBuilder;

    /**
     * @var OrderHandlerService
     */
    public $orderHandler;

    /**
     * Class Order constructor 
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Naxero\AdvancedInstantPurchase\Model\Service\OrderHandlerService $orderHandler
    ) {
        parent::__construct($context);

        $this->customerSession = $customerSession;
        $this->formKeyValidator = $formKeyValidator;
        $this->urlBuilder = $urlBuilder;
        $this->orderHandler = $orderHandler;
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
            // Create the order
            $order = $this->orderHanlder->createOrder($paymentData);

        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $this->createResponse($this->createGenericErrorMessage(), false);
        } catch (\Exception $e) {
            return $this->createResponse(
                $e instanceof Magento\Framework\Exception\LocalizedException ? $e->getMessage() : $this->createGenericErrorMessage(),
                false
            );
        }

        // Order confirmation
        $message = json_encode([
            'order_url' => $this->urlBuilder->getUrl('sales/order/view/order_id/' . $order->getId()),
            'order_increment_id' => $order->getIncrementId()
        ]);
        
        return $this->createResponse($message, true);
    }

    /**
     * Creates error message without exposing error details.
     *
     * @return string
     */
    public function createGenericErrorMessage(): string
    {
        return (string)__('Something went wrong while processing your order. Please try again later.');
    }

    /**
     * Checks if all parameters that should be handled are passed.
     *
     * @param RequestInterface $request
     * @return bool
     */
    public function doesRequestContainAllKnowParams(RequestInterface $request): bool
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
    public function getRequestUnknownParams(RequestInterface $request): array
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
    public function createResponse(string $message, bool $successMessage): JsonResult
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
