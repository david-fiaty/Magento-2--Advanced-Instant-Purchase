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

namespace Naxero\BuyNow\Setup;

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

    /**
     * InstallData constructor.
     */
    public function __construct(
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Cms\Model\BlockRepository $blockRepository
    ) {
        $this->blockFactory = $blockFactory;
        $this->blockRepository = $blockRepository;
    }

    /**
     * Install the module data.
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        // Start the setup
        $setup->startSetup();

        // Prepare the data
        $cmsBlockData = [
            'title' => 'Naxero Buy Now Button',
            'identifier' => 'naxero_buy_now_button_1',
            'content' => '{BuyNow product_id="1"}',
            'is_active' => 1,
            'stores' => [0],
            'sort_order' => 0
        ];

        // Handle the block creation
        try {
            $this->blockRepository->getById($cmsBlockData['identifier']);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->blockFactory->create()->setData($cmsBlockData)->save();
        }

        // End the setup
        $setup->endSetup();
    }
}
