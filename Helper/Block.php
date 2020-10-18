<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

/**
 * Class Block helper.
 */
class Block extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var CustomerAddressesFormatter
     */
    public $customerAddressesFormatter;

    /**
     * Class Block helper constructor.
     */
    public function __construct(
        \Magento\InstantPurchase\Model\Ui\CustomerAddressesFormatter $customerAddressesFormatter
    ) {
        $this->customerAddressesFormatter = $customerAddressesFormatter;
    }

}