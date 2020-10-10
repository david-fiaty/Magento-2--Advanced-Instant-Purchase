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
        \Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper
    ) {
        $this->customerHelper = $customerHelper;
    }

    /**
     * Place an order.
     */
    public function placeOrder($store, $product, $productRequest, $paymentData) {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/paymentData.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($paymentData, 1));

        // Get the addressses
        $shippingAddress = $this->customerHelper->getShippingAddress($paymentData['shippingAddressId']);
        $billingAddress = $this->customerHelper->getBillingAddress($paymentData['billingAddressId']);
        
        // Prepare the shipping address
        $shippingAddress->setCollectShippingRates(true);
        $shippingAddress->setShippingMethod($paymentData['shippingMethodCode']);
    }
}
