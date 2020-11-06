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

namespace Naxero\BuyNow\Model\Payment\Integration\Free;

/**
 * Class Index.
 */
class Index implements \Naxero\BuyNow\Model\Payment\Integration\PaymentIntegrationInterface
{
    /**
     * @var QuoteManagement
     */
    public $quoteManagement;

    /**
     * @var string|array|object
     */
    public $response;

    /**
     * Class Index constructor.
     */
    public function __construct(
        \Magento\Quote\Model\QuoteManagement $quoteManagement
    ) {
        $this->quoteManagement = $quoteManagement;
    }

    /**
     * Send a payment request.
     */
    public function sendRequest($quote, $paymentData)
    {
        // Get the payment response
        $response = '';

        // Assign the response to self
        $this->response = $response;

        return $this;
    }

    /**
     * Chcek if a payment request is successful.
     */
    public function paymentSuccess()
    {
        return true;
    }

    /**
     * Create a new order.
     */
    public function createOrder($quote, $paymentResponse)
    {
        $order = $this->quoteManagement->submit($quote);

        return $order;
    }
}
