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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

/**
 * Instant Purchase order placement.
 *
 */
class Request extends \Magento\Framework\App\Action\Action
{
    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var Validator
     */
    public $formKey;

    /**
     * @var ProductRepositoryInterface
     */
    public $productRepository;

    /**
     * @var OrderRepositoryInterface
     */
    public $orderRepository;

    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @var Curl
     */
    public $curl;
    
    /**
     * Request class constructor
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKey,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\HTTP\Client\Curl $curl
    ) {
        parent::__construct($context);

        $this->storeManager = $storeManager;
        $this->formKey = $formKey;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
        $this->customerSession = $customerSession;
        $this->curl = $curl;
    }

    /**
     * Place an order for a customer.
     *
     * @return JsonResult
     */
    public function execute()
    {
        $request = $this->getRequest();
        /*
        if (!$this->formKeyValidator->validate($request)) {
            return $this->createResponse($this->createGenericErrorMessage(), false);
        }
        */

        // Get the request parameters
        $params = $request->getParams();

        // Todo - Remove test
        /*
        return $this->createResponse(
            json_encode($request->getParams()),
            false
        );
        */

        // Get the product id
        $productId = (int) $params['product'];

        $paymentTokenPublicHash = (string)$params['nbn-payment-method-select'];
        $shippingAddressId = (int)$params['nbn-shipping-address-select'];
        $billingAddressId = (int)$params['nbn-billing-address-select'];
        $carrierCode = (string)$params['nbn-shipping-method-select'];
        $shippingMethodCode = (string)$params['nbn-shipping-method-select'];

        try {
            $customer = $this->customerSession->getCustomer();
            $store = $this->storeManager->getStore();
            $product = $this->productRepository->getById(
                $productId,
                false,
                $store->getId(),
                false
            );

            $orderId = '//place order call';
        } catch (\Exception $e) {
            return $this->createResponse(
                $e instanceof LocalizedException ? $e->getMessage() : $this->createGenericErrorMessage(),
                false
            );
        }

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
     * Creates response with a operation status message.
     *
     * @param string $message
     * @param bool $successMessage
     * @return JsonResult
     */
    public function createResponse(string $message, bool $successMessage): JsonResult
    {
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
