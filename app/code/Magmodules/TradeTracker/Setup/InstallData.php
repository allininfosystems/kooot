<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\TradeTracker\Setup;

use Magento\Catalog\Model\Category;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Config\Model\ResourceModel\Config as ConfigModel;

/**
 * Class InstallData
 *
 * @package Magmodules\TradeTracker\Setup
 */
class InstallData implements InstallDataInterface
{

    /**
     * @var ConfigModel
     */
    private $config;
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * InstallData constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param ConfigModel          $config
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        ConfigModel $config
    ) {
        $this->config = $config;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            Category::ENTITY,
            'tt_cat_disable_export',
            [
                'type'         => 'int',
                'label'        => 'TradeTracker: Disable Export',
                'input'        => 'select',
                'source'       => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'global'       => 1,
                'visible'      => true,
                'required'     => false,
                'user_defined' => false,
                'sort_order'   => 100,
                'default'      => 0
            ]
        );
    }
}
