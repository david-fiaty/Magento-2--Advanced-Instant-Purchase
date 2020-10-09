<?php
namespace Naxero\AdvancedInstantPurchase\Model\Service;

/**
 * Place order service class.
 *
 * @api
 * @since 100.2.0
 */
class PlaceOrderService
{
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var QuoteCreation
     */
    private $quoteCreation;

    /**
     * @var QuoteFilling
     */
    private $quoteFilling;

    /**
     * @var ShippingConfiguration
     */
    private $shippingConfiguration;

    /**
     * @var PaymentConfiguration
     */
    private $paymentConfiguration;

    /**
     * @var Purchase
     */
    private $purchase;

    /**
     * @var Customer
     */
    public $customerHelper;

    /**
     * PlaceOrder constructor.
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\InstantPurchase\Model\QuoteManagement\QuoteCreation $quoteCreation,
        \Magento\InstantPurchase\Model\QuoteManagement\QuoteFilling $quoteFilling,
        \Magento\InstantPurchase\Model\QuoteManagement\ShippingConfiguration $shippingConfiguration,
        \Magento\InstantPurchase\Model\QuoteManagement\PaymentConfiguration $paymentConfiguration,
        \Magento\InstantPurchase\Model\QuoteManagement\Purchase $purchase,
        \Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->quoteCreation = $quoteCreation;
        $this->quoteFilling = $quoteFilling;
        $this->shippingConfiguration = $shippingConfiguration;
        $this->paymentConfiguration = $paymentConfiguration;
        $this->purchase = $purchase;
        $this->customerHelper = $customerHelper;
    }

    /**
     * Place an order.
     */
    public function placeOrder($store, $customer, $product, $productRequest, $paymentData) {
        // Get the addressses
        $shippingAddress = $this->customerHelper->getShippingAddress($paymentData['shippingAddressId']);
        $billingAddress = $this->customerHelper->getBillingAddress($paymentData['billingAddressId']);
        
        // Prepare the shipping address
        $shippingAddress->setCollectShippingRates(true);
        $shippingAddress->setShippingMethod($paymentData['shippingMethodCode']);

        // Create the quote
        $quote = $this->quoteCreation->createQuote(
            $store,
            $customer,
            $shippingAddress,
            $billingAddress
        );

        // Set the payment method
        $payment = $quote->getPayment();
        $payment->setMethod($paymentData['paymentMethodCode']);
        $payment->save();
        $quote->save();

        // Fill the quote
        $quote = $this->quoteFilling->fillQuote(
            $quote,
            $product,
            $productRequest
        );

        // Validate the quote
        $quote->collectTotals();
        $this->quoteRepository->save($quote);
        $quote = $this->quoteRepository->get($quote->getId());

        // Run the logic
        try {



            return $orderId;
        } catch (\Throwable $e) {
            $quote->setIsActive(false);
            $this->quoteRepository->save($quote);
            throw $e;
        }
    }
}
