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
     * Get a discount amount from a coupon code.
     */
    public function getDiscountAmount($couponCode)
    {
        $ruleId =  $this->couponModel->loadByCode($couponCode)->getRuleId();
        $rule = $this->ruleRepository->getById($ruleId);
        return $rule->getDiscountAmount();
    }
}
