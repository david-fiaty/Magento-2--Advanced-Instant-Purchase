<?php
namespace Naxero\AdvancedInstantPurchase\Block\Button;

/**
 * Configuration for JavaScript instant purchase button component.
 */
class ListButton extends \Naxero\AdvancedInstantPurchase\Block\Button\AbstractButton
{
    /**
     * ListButton class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get the block config.
     */
    public function getConfig()
    {
        $config = $this->configHelper->getValues();
        $condition = $config['guest']['show_guest_button']
        && $config['general']['enabled']
        && $config['display']['product_list']
        && $this->productHelper->isListView();

        return $condition ? $config : null;
    }
}
