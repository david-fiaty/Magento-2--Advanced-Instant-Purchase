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

namespace Naxero\BuyNow\Model\Config\Backend\Source;

/**
 * Class CategoryList
 */
class CategoryList implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Category
     */
    public $categoryHelper;

    /**
     * CategoryList constructor.
     */
    public function __construct(
        \Naxero\BuyNow\Helper\Category $categoryHelper
    ) {
        $this->categoryHelper = $categoryHelper;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->categoryHelper->getRootCategories();
    }
}
