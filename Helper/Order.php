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

namespace Naxero\BuyNow\Helper;

/**
 * Class Order helper.
 */
class Order extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Coupon
     */
    public $couponModel;

    /**
     * @var RuleRepositoryInterface
     */
    public $ruleRepository;

    /**
     * Class Order helper constructor.
     */
    public function __construct(
        \Magento\SalesRule\Model\Coupon $couponModel,
        \Magento\SalesRule\Api\RuleRepositoryInterface $ruleRepository
    ) {
        $this->couponModel = $couponModel;
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * Apply a discount code to an amount.
     */
    public function applyDiscount($rule, $amount)
    {
        $discountedAmount = $amount - (float) $rule->getDiscountAmount();
        return $discountedAmount > 0 ? $discountedAmount : 0;
    }

    /**
     * Get a coupon code data.
     */
    public function getCouponRule($couponCode)
    {
        $ruleId =  $this->couponModel->loadByCode($couponCode)->getRuleId();
        if ((int) $ruleId > 0) {
            return $this->ruleRepository->getById($ruleId);
        }

        return null;
    }
}
