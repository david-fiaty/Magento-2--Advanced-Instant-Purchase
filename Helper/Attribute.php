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
 * Class Attribute helper.
 */
class Attribute extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    public $eavConfig;

    /**
     * @var \Magento\Swatches\Helper\Data
     */
    public $swatchHelper;

    /**
     * @var Config
     */
    public $configHelper;

    /**
     * Class Product helper constructor.
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Naxero\BuyNow\Helper\Config $configHelper
    ) {
        $this->eavConfig = $eavConfig;
        $this->swatchHelper = $swatchHelper;
        $this->configHelper = $configHelper;
    }

    /**
     * Check if a product attribute is swatch.
     */
    public function isSwatch($code)
    {
        $attribute = $this->eavConfig->getAttribute('catalog_product', $code);
        return $this->swatchHelper->isSwatchAttribute($attribute);
    }

    /**
     * Get a product attribute type.
     */
    public function getAttributeType($code)
    {
        // Todo - Handle other attribute types
        return $this->isSwatch($code) ? 'swatch' : 'select';
    }

    /**
     * Add the product attribute data to an option.
     */
    public function addAttributeData($option)
    {
        $option['attribute_type'] = $this->getAttributeType($option['attribute_code']);

        return $option;
    }
}
