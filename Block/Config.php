<?php
namespace Naxero\AdvancedInstantPurchase\Block;

/**
 * Configuration for JavaScript instant purchase button component.
 */
class Config extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Config
     */
    public $configHelper;

    /**
     * @var Customer
     */
    public $customerHelper;

    /**
     * Button class constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Naxero\AdvancedInstantPurchase\Helper\Config $configHelper,
        \Naxero\AdvancedInstantPurchase\Helper\Customer $customerHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configHelper = $configHelper;
        $this->customerHelper = $customerHelper;
    }

    /**
     * Get the module config values.
     */
    public function getConfig()
    {
        return json_encode(
            array_merge(
                $this->configHelper->getFrontendValues(),
                $this->getLoginStatus()
            )
        );
    }

    /**
     * Get the current user status.
     */
    public function getLoginStatus()
    {
        return [
            'user' => [
                'connected' => $this->customerHelper->isLoggedIn()
            ]
        ];
    }
}
