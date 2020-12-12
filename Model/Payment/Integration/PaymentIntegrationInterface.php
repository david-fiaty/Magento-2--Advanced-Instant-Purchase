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

namespace Naxero\BuyNow\Model\Payment\Integration;

/**
 * Interface PaymentIntegrationInterface.
 */
interface PaymentIntegrationInterface
{
    /**
     * Send a payment request.
     *
     * @param Quote $quote
     * @param array $paymentData
     */
    public function sendRequest($quote, $paymentData);

    /**
     * Check if a payment is successful.
     */
    public function paymentSuccess();

    /**
     * Create an order.
     *
     * @param Quote               $quote
     * @param string|array|object $paymentResponse
     */
    public function createOrder($quote, $paymentResponse);
}
