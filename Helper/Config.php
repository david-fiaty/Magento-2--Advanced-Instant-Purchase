<?php
namespace Naxero\InstantPurchase\Helper;

/**
 * Class Connfig
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function value($group, $field)
    {
        $path = 'default/advanced_instant_purchase/'. $group . '/' . $field;
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
