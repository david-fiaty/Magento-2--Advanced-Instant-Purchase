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
     * @var PlaceOrderService
     */
    public $placeOrderService;

    /**
     * Request class constructor
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Framework\Data\Form\FormKey\Validator $formKey,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Naxero\BuyNow\Model\Service\PlaceOrderService $placeOrderService
    ) {
        parent::__construct($context);

        $this->jsonFactory = $jsonFactory;
        $this->formKey = $formKey;
        $this->productRepository = $productRepository;
        $this->customerSession = $customerSession;
        $this->placeOrderService = $placeOrderService;
    }

    /**
     * Place an order for a customer.
     *
     * @return JsonResult
     */
    public function execute()
    {
        // Place the order
        $params = $this->getRequestParams();
        if ($params) {
            try {
                $order = $this->placeOrderService->placeOrder($params);
                if ($order) {
                    $message = __('Your order number is: %1.', $order->getIncrementId());
                }
                else {
                    $message = __('The payment could not be processed.');
                }
            } 
            catch (\Exception $e) {
                return $this->createResponse($e->getMessage(), false);
            }

        }

        return $this->createResponse($message, true);
    }

    /**
     * Get request params.
     */
    public function getRequestParams()
    {
        // Get the request parameters
        $request = $this->getRequest();
        $params = $request->getParams();
        if (isset($params['product']) && (int) $params['product'] > 0) {
            return $params;
        }

        return null;
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
     * Creates response with a operation status message.
     */
    public function createResponse($message, $successMessage)
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
