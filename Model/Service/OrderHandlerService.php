<?php
namespace Naxero\AdvancedInstantPurchase\Model\Service;

/**
 * Class OrderHandlerService.
 */
class OrderHandlerService
{
    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    public $productRepository;
    
    /**
     * @var QuoteCreation
     */
    public $quoteCreation;

    /**
     * @var QuoteFilling
     */
    public $quoteFilling;

    /**
     * @var QuoteManagement
     */
    public $quoteManagement;
    
    /**
     * @var CartRepositoryInterface
     */
    public $quoteRepository;

    /**
     * @var Customer
     */
    public $customerHelper;

    /**
     * OrderHandlerService constructor.
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\InstantPurchase\Model\QuoteManagement\QuoteCreation $quoteCreation,
        \Magento\InstantPurchase\Model\QuoteManagement\QuoteFilling $quoteFilling,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper
    ) {
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->quoteCreation = $quoteCreation;
        $this->quoteFilling = $quoteFilling;
        $this->quoteManagement = $quoteManagement;
        $this->quoteRepository = $quoteRepository;
        $this->customerHelper = $customerHelper;
    }

    /**
     * Place an order.
     */
    public function placeOrder($paymentData) {
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
            $payment = $quote->getPayment();
            $payment->setMethod($paymentData['paymentMethodCode']);
            $payment->importData([
                'method' => $paymentData['paymentMethodCode']
            ]);
            $payment->save();
            $quote->save();

            // Save the quote
            $quote->collectTotals();
            $this->quoteRepository->save($quote);
            $quote = $this->quoteRepository->get($quote->getId());

            // Create the order
            $order = $this->createOrder($quote);
            
            return $order;
    }

    /**
     * Create a new order.
     */
    public function createOrder($quote) {
        $order = $this->quoteManagement->submit($quote);
        return $order;
    }
}
