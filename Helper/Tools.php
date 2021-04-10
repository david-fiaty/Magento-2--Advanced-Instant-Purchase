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
 * Class Tools helper.
 */
class Tools extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var FormKey
     */
    public $formKey;

    /**
     * @var Data
     */
    public $priceHelper;

    /**
     * Class Tools helper constructor.
     */
    public function __construct(
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    ) {
        $this->formKey = $formKey;
        $this->priceHelper = $priceHelper;
    }

    /**
     * Generate a form key.
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * Render an amount.
     */
    public function renderAmount($amount, $format = true, $includeContainer = false)
    {
        return $this->priceHelper->currency(
            $amount,
            $format,
            $includeContainer
        );
        ;
    }

    /**
     * Get array keys recursively.
     */
    public function arrayKeysRecursive(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $index[$key] = $this->arrayKeysRecursive($value);
            } else {
                $index[] = $key;
            }
        }

        return $index ?? [];
    }
}
