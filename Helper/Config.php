<?php
namespace Naxero\AdvancedInstantPurchase\Helper;

/**
 * Class Connfig
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function value($field)
    {
        $path = 'default/advanced_instant_purchase/general/' . $field;
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
