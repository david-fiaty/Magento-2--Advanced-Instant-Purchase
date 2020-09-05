<?php
namespace Naxero\AdvancedInstantPurchase\Block\Confirmation;

/**
 * Display Class.
 */
class Display extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Customer
     */
    public $customerHelper;

    /**
     * Display constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerHelper = $customerHelper;
    }

    /**
     * Get the connfirmation popup content.
     */
    public function getConfirmContent() {
        return $this->customerHelper->getConfirmContent();
    }
}
