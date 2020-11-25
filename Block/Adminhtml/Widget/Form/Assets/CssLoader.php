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

namespace Naxero\BuyNow\Block\Adminhtml\Widget\Form\Assets;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Naxero\BuyNow\Model\Config\Naming;

/**
 * CssLoader class.
 */
class CssLoader extends \Magento\Backend\Block\Template
{
    /**
     * @var Repository
     */
    public $assetRepository;

    /**
     * CssLoader class constructor.
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\View\Asset\Repository $assetRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
    
        $this->assetRepository = $assetRepository;
    }

    /**
     * Render the widget field.
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        // Prepare the separator HTML
        $blockHtml = $this->getLayout()->createBlock('Magento\Backend\Block\Template')
            ->setTemplate(Naming::getModuleName() . '::widget/form/assets/css-loader.phtml')
            ->setData('element', $element)
            ->toHtml();

        // Render the HTML
        $element->setData('after_element_html', $blockHtml);

        return $element;
    }

    /**
     * Get the CSS assets URLs.
     */
    public function getCssAssets() {
        return [
            $this->assetRepository->getUrl(Naming::getModuleName() . '::css/lib/select2/select2.css')
        ];
    }
}
