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
    public $entityAttribute;

    /**
     * @var \Magento\Swatches\Helper\Data
     */
    public $swatchHelper;

    /**
     * Class Product helper constructor.
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Attribute $entityAttribute, 
        \Magento\Swatches\Helper\Data $swatchHelper
    ) {
        $this->entityAttribute = $entityAttribute;
        $this->swatchHelper = $swatchHelper;
    }

    /**
     * Check if a product attribute is swatch.
     */
    public function isSwatch($attributeCode)
    {
        $attribute = $this->entityAttribute->loadByCode('catalog_product', $attributeCode);

        return $this->swatchHelper->isSwatchAttribute($attribute);
    }
}
