<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\TradeTracker\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Category;

/**
 * Class Uninstall
 *
 * @package Magmodules\TradeTracker\Setup
 */
class Uninstall implements UninstallInterface
{

    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * Uninstall constructor.
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var \Magento\Catalog\Setup\CategorySetup $categorySetupManager */
        $categorySetupManager = $this->categorySetupFactory->create();
        $categorySetupManager->removeAttribute(Category::ENTITY, 'tt_cat_disable_export');
        $categorySetupManager->removeAttribute(Category::ENTITY, 'tt_product_id');
        $categorySetupManager->removeAttribute(Category::ENTITY, 'tt_category');
        $categorySetupManager->removeAttribute(Product::ENTITY, 'tt_product_id');
        $categorySetupManager->removeAttribute(Product::ENTITY, 'tt_exclude');

        $entityTypeId = $categorySetupManager->getEntityTypeId('catalog_product');
        $attributeSetIds = $categorySetupManager->getAllAttributeSetIds($entityTypeId);

        foreach ($attributeSetIds as $attributeSetId) {
            $categorySetupManager->removeAttributeGroup($entityTypeId, $attributeSetId, 'TradeTracker');
        }

        $setup->getConnection()->delete(
            $setup->getTable('core_config_data'),
            ['path LIKE ?' => 'magmodules_tradetracker/%']
        );

        $setup->endSetup();
    }
}
