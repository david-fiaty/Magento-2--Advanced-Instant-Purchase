<?php

namespace Naxero\AdvancedInstantPurchase\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{
    private $blockFactory;

    public function __construct(
        \Magento\Cms\Model\BlockFactory $blockFactory
    )
    {
        $this->blockFactory = $blockFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $cmsBlockData = [
            'title' => 'Buy Now ',
            'identifier' => 'naxero_aip_purchase_block',
            'content' => "{BuyNow}",
            'is_active' => 1,
            'stores' => [0],
            'sort_order' => 0
        ];

        $this->blockFactory->create()->setData($cmsBlockData)->save();
    }
} 