<?php

namespace Naxero\AdvancedInstantPurchase\Setup;

use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\BlockRepository;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var BlockRepository
     */
    private $blockRepository;

    public function __construct(BlockFactory $blockFactory, BlockRepository $blockRepository)
    {
        $this->blockFactory = $blockFactory;
        $this->blockRepository = $blockRepository;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $cmsBlockData = [
            'title' => 'Buy Now Button',
            'identifier' => 'naxero_buy_now_button_1',
            'content' => '{BuyNow product_id="1"}',
            'is_active' => 1,
            'stores' => [0],
            'sort_order' => 0
        ];

        try {
            $this->blockRepository->getById($cmsBlockData['identifier']);
        } catch (NoSuchEntityException $e) {
            $this->blockFactory->create()->setData($cmsBlockData)->save();
        }

        $setup->endSetup();
    }
} 