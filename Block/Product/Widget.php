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

namespace Naxero\BuyNow\Block\Product;

/**
 * Widget class constructor.
 */
class Widget extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Block
     */
    public $blockHelper;

    /**
     * @var Product
     */
    public $productHelper;

    /**
     * Widget class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Naxero\BuyNow\Helper\Block $blockHelper,
        \Naxero\BuyNow\Helper\Product $productHelper,
        array $data = []
    ) {

        parent::__construct($context, $data);

        $this->blockHelper = $blockHelper;
        $this->productHelper = $productHelper;
    }

    /**
     * Get the block config.
     */
    public function getConfig()
    {
        // Get the base the config
        $config = $this->blockHelper->getConfig(
            $this->getData('product_id')
        );

        // Update with block config
        return $this->blockHelper->updateAttributesData($config);
    }

    /**
     * Get a custom option HTML.
     */
    public function getOptionsHtml($productId)
    {
        // Prepare parameters
        $layout = $this->getLayout();
        $product = $this->productHelper->getProduct($productId);

        // Block data
        $blockOptionData = $layout->createBlock('Magento\Catalog\Block\Product\View\Options')
        ->setProduct($product)
        ->setTemplate('Magento_Catalog::product/view/options.phtml');

        // Set options renderers
        $blockOptionData->setChild('select', $this->getOptionBlock($layout, 'select'));
        $blockOptionData->setChild('text', $this->getOptionBlock($layout, 'text'));

        return $blockOptionData->toHtml();
    }

    /**
     * Get an option block.
     */
    public function getOptionBlock($layout, $type)
    {        
        // Select list
        if ($type == 'select') {
            return $layout->createBlock('Magento\Catalog\Block\Product\View\Options\Type\Select', 'select')
            ->setTemplate('Magento_Catalog::product/view/options/type/select.phtml');
        }

        // Text field
        if ($type == 'text') {
            return $layout->createBlock('Magento\Catalog\Block\Product\View\Options\Type\Text', 'text')
            ->setTemplate('Magento_Catalog::product/view/options/type/text.phtml');
        }
    }
}
