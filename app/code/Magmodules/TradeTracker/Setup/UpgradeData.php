<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\TradeTracker\Setup;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Config\ValueInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magmodules\TradeTracker\Helper\General as GeneralHelper;

/**
 * Class UpgradeData
 *
 * @package Magmodules\TradeTracker\Setup
 */
class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var ValueInterface
     */
    private $configReader;
    /**
     * @var WriterInterface
     */
    private $configWriter;
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var GeneralHelper
     */
    private $generalHelper;

    /**
     * UpgradeData constructor.
     *
     * @param EavSetupFactory          $eavSetupFactory
     * @param ProductMetadataInterface $productMetadata
     * @param ObjectManagerInterface   $objectManager
     * @param ValueInterface           $configReader
     * @param WriterInterface          $configWriter
     * @param ProductCollectionFactory $productCollectionFactory
     * @param GeneralHelper            $generalHelper
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        ProductMetadataInterface $productMetadata,
        ObjectManagerInterface $objectManager,
        ValueInterface $configReader,
        WriterInterface $configWriter,
        ProductCollectionFactory $productCollectionFactory,
        GeneralHelper $generalHelper
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->productMetadata = $productMetadata;
        $this->objectManager = $objectManager;
        $this->configReader = $configReader;
        $this->configWriter = $configWriter;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->generalHelper = $generalHelper;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->changeConfigPaths();
            $magentoVersion = $this->productMetadata->getVersion();
            if (version_compare($magentoVersion, '2.2.0', '>=')) {
                $this->convertSerializedDataToJson($setup);
            }

            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                Category::ENTITY,
                'tt_product_id',
                [
                    'type'         => 'varchar',
                    'label'        => 'TradeTracker: Product Category (conversion)',
                    'input'        => 'text',
                    'global'       => 1,
                    'visible'      => true,
                    'required'     => false,
                    'user_defined' => false,
                    'sort_order'   => 100,
                    'default'      => null
                ]
            );
            $eavSetup->addAttribute(
                Category::ENTITY,
                'tt_category',
                [
                    'type'         => 'varchar',
                    'label'        => 'TradeTracker: Category (feed)',
                    'input'        => 'text',
                    'group'        => 'General Information',
                    'global'       => 1,
                    'visible'      => true,
                    'required'     => false,
                    'user_defined' => false,
                    'sort_order'   => 100,
                    'default'      => null
                ]
            );

            $groupName = 'TradeTracker';
            $entityTypeId = $eavSetup->getEntityTypeId('catalog_product');
            $attributeSetIds = $eavSetup->getAllAttributeSetIds($entityTypeId);
            foreach ($attributeSetIds as $attributeSetId) {
                $eavSetup->addAttributeGroup($entityTypeId, $attributeSetId, $groupName, 1001);
            }

            $eavSetup->addAttribute(
                Product::ENTITY,
                'tt_exclude',
                [
                    'group'                   => $groupName,
                    'type'                    => 'int',
                    'label'                   => 'Exclude for TradeTracker',
                    'input'                   => 'select',
                    'source'                  => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'global'                  => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'default'                 => Boolean::VALUE_NO,
                    'required'                => false,
                    'searchable'              => false,
                    'filterable'              => false,
                    'comparable'              => false,
                    'visible_on_front'        => false,
                    'used_in_product_listing' => false,
                    'unique'                  => false,
                ]
            );

            $attribute = $eavSetup->getAttribute($entityTypeId, 'tt_exclude');
            foreach ($attributeSetIds as $attributeSetId) {
                $eavSetup->addAttributeToGroup(
                    $entityTypeId,
                    $attributeSetId,
                    $groupName,
                    $attribute['attribute_id'],
                    110
                );
            }

            try {
                $productIds = $this->productCollectionFactory->create()->getAllIds();
                /** @var \Magento\Catalog\Model\Product\Action $action */
                $this->objectManager
                    ->get('\Magento\Catalog\Model\Product\Action')
                    ->updateAttributes(
                        $productIds,
                        ['tt_exclude' => 0],
                        0
                    );
            } catch (\Exception $e) {
                $this->generalHelper->addTolog('addProductAtributes', $e->getMessage());
            }

            $eavSetup->addAttribute(
                Product::ENTITY,
                'tt_product_id',
                [
                    'group'                   => $groupName,
                    'type'                    => 'varchar',
                    'label'                   => 'TradeTracker ProductID (conversion)',
                    'input'                   => 'text',
                    'source'                  => '',
                    'global'                  => ScopedAttributeInterface::SCOPE_WEBSITE,
                    'visible'                 => true,
                    'required'                => false,
                    'user_defined'            => true,
                    'default'                 => '',
                    'searchable'              => false,
                    'filterable'              => false,
                    'comparable'              => false,
                    'visible_on_front'        => false,
                    'used_in_product_listing' => true,
                    'unique'                  => false,
                ]
            );

            $attribute = $eavSetup->getAttribute($entityTypeId, 'tt_product_id');
            foreach ($attributeSetIds as $attributeSetId) {
                $eavSetup->addAttributeToGroup(
                    $entityTypeId,
                    $attributeSetId,
                    $groupName,
                    $attribute['attribute_id'],
                    112
                );
            }
        }
    }

    /**
     * Change config paths for fields due to changes in config options.
     */
    public function changeConfigPaths()
    {
        $collection = $this->configReader->getCollection()
            ->addFieldToFilter("path", "magmodules_tradetracker/advanced/parent_atts");

        foreach ($collection as $config) {
            /** @var \Magento\Framework\App\Config\Value $config */
            $this->configWriter->save(
                "magmodules_tradetracker/types/configurable_parent_atts",
                $config->getValue(),
                $config->getScope(),
                $config->getScopeId()
            );
            $this->configWriter->delete(
                "magmodules_tradetracker/advanced/parent_atts",
                $config->getScope(),
                $config->getScopeId()
            );
        }

        $collection = $this->configReader->getCollection()
            ->addFieldToFilter("path", "magmodules_tradetracker/advanced/relations");

        foreach ($collection as $config) {
            /** @var \Magento\Framework\App\Config\Value $config */
            if ($config->getValue() == 1) {
                $this->configWriter->save(
                    "magmodules_tradetracker/types/configurable",
                    'simple',
                    $config->getScope(),
                    $config->getScopeId()
                );
            }
            $this->configWriter->delete(
                "magmodules_tradetracker/advanced/relations",
                $config->getScope(),
                $config->getScopeId()
            );
        }

        $collection = $this->configReader->getCollection()
            ->addFieldToFilter("path", "magmodules_tradetracker/general/enable")
            ->addFieldToFilter("scope_id", ["neq" => 0]);

        foreach ($collection as $config) {
            /** @var \Magento\Framework\App\Config\Value $config */
            $this->configWriter->delete(
                "magmodules_tradetracker/general/enable",
                $config->getScope(),
                $config->getScopeId()
            );
        }
    }

    /**
     * Convert Serialzed Data fields to Json for Magento 2.2
     * Using Object Manager for backwards compatability
     *
     * @param ModuleDataSetupInterface $setup
     */
    private function convertSerializedDataToJson(ModuleDataSetupInterface $setup)
    {
        $fieldDataConverter = $this->objectManager
            ->create(\Magento\Framework\DB\FieldDataConverterFactory::class)
            ->create(\Magento\Framework\DB\DataConverter\SerializedToJson::class);

        $queryModifier = $this->objectManager
            ->create(\Magento\Framework\DB\Select\QueryModifierFactory::class)
            ->create(
                'in',
                [
                    'values' => [
                        'path' => [
                            'magmodules_tradetracker/advanced/extra_fields',
                            'magmodules_tradetracker/advanced/shipping',
                            'magmodules_tradetracker/filter/filters_data'
                        ]
                    ]
                ]
            );

        $fieldDataConverter->convert(
            $setup->getConnection(),
            $setup->getTable('core_config_data'),
            'config_id',
            'value',
            $queryModifier
        );
    }
}
