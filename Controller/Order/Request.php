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

/**
 * Instant Purchase order placement.
 *
 */
class Request extends \Magento\Framework\App\Action\Action
{
    /**
     * @var JsonFactory
     */
    public $jsonFactory;

    /**
     * @var Validator
     */
    public $formKey;

    /**
     * @var ProductRepositoryInterface
     */
    public $productRepository;

    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @var Order
     */
    public $orderHelper;

    /**
     * Request class constructor
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Framework\Data\Form\FormKey\Validator $formKey,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Naxero\BuyNow\Helper\Order $orderHelper
    ) {
        parent::__construct($context);

        $this->jsonFactory = $jsonFactory;
        $this->formKey = $formKey;
        $this->productRepository = $productRepository;
        $this->customerSession = $customerSession;
        $this->orderHelper = $orderHelper;
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

        // Get the request parameters
        $params = $request->getParams();

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/1.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($params, 1));

        // Todo - Check why payment request called twice, once with empty array
        if (isset($params['product']) && (int) $params['product'] > 0) {
            // Prepare the order parameters
            $productId = $params['product'];
            $paymentTokenPublicHash = (string)$params['nbn-payment-method-select'];
            $shippingAddressId = (int)$params['nbn-shipping-address-select'];
            $billingAddressId = (int)$params['nbn-billing-address-select'];
            $carrierCode = (string)$params['nbn-shipping-method-select'];
            $shippingMethodCode = (string)$params['nbn-shipping-method-select'];

            // Place the order
            try {
                $order = $this->orderHelper->placeOrder($productId);
            } 
            catch (\Exception $e) {
                return $this->createResponse(
                    $e instanceof LocalizedException ? $e->getMessage() : $this->createGenericErrorMessage(),
                    false
                );
            }

            $message = __('Your order number is: %1.', $order->getIncrementId());
        }
        else {
            $message = __('No product found for the payment request');
        }

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
        // Prepare the result
        $result = $this->jsonFactory->create()->setData([
            'response' => $message
        ]);

        // Handle the response
        if ($successMessage) {
            $this->messageManager->addSuccessMessage($message);
        } else {
            $this->messageManager->addErrorMessage($message);
        }

        return $result;
    }
}
