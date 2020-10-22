<?php

namespace Naxero\AdvancedInstantPurchase\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{
    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var BlockRepository
     */
    private $blockRepository;

    public function __construct(
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Cms\Model\BlockRepository $blockRepository
    )
    {
        $this->blockFactory = $blockFactory;
        $this->blockRepository = $blockRepository;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $cmsBlockData = [
            'title' => 'Naxero Buy Now Button',
            'identifier' => 'naxero_buy_now_button_1',
            'content' => '{BuyNow product_id="1"}',
            'is_active' => 1,
            'stores' => [0],
            'sort_order' => 0
        ];

        try {
            $this->blockRepository->getById($cmsBlockData['identifier']);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->blockFactory->create()->setData($cmsBlockData)->save();
        }

        $setup->endSetup();
    }
} 