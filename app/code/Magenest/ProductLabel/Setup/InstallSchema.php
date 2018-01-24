<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ProductLabel
 * @author   ThaoPV <thaopw@gmail.com>
 */
namespace Magenest\ProductLabel\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
 *
 * @package Magenest\ProductLabel\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (!$installer->tableExists('magenest_productlabel_rule')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magenest_productlabel_rule')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'identity' => true, 'nullable' => false, 'primary' => true],
                'Label ID'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Rule Name'
            )->addColumn(
                'type',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Type'
            )->addColumn(
                'description',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Description'
            )->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                null,
                [],
                'Status'
            )->addColumn(
                'from_date',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'From Date'
            )->addColumn(
                'to_date',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'To Date'
            )->addColumn(
                'priority',
                Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'Priority'
            )->addColumn(
                'conditions_serialized',
                Table::TYPE_TEXT,
                '2M',
                ['nullable' => true],
                'Conditions Serialized'
            )->addColumn(
                'category_display',
                Table::TYPE_INTEGER,
                1,
                ['nullable' => false],
                'Category Display'
            )->addColumn(
                'category_position',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Category Position'
            )->addColumn(
                'category_image',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Category Image'
            )->addColumn(
                'category_text',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Category Text'
            )->addColumn(
                'product_display',
                Table::TYPE_INTEGER,
                1,
                ['nullable' => false],
                'Product Display'
            )->addColumn(
                'product_position',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Category Position'
            )->addColumn(
                'product_image',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Product Image'
            )->addColumn(
                'product_text',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Product Text'
            )->setComment(
                'Product Labels Table'
            );
            $installer->getConnection()->createTable($table);
        }

        /**
         * Create table 'magenest_productlabel_store'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('magenest_productlabel_store'))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )
            ->addIndex(
                $installer->getIdxName('magenest_productlabel_store', ['store_id']),
                ['store_id']
            )
            ->addForeignKey(
                $installer->getFkName('magenest_productlabel_store', 'rule_id', 'magenest_productlabel_rule', 'id'),
                'rule_id',
                $installer->getTable('magenest_productlabel_rule'),
                'id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('magenest_productlabel_store', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Product Label Rules To Store Relations');

        $installer->getConnection()->createTable($table);


        /**
         * Create table 'magenest_productlabel_customer_group'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('magenest_productlabel_customer_group'))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule Id'
            )
            ->addColumn(
                'customer_group_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Customer Group Id'
            )
            ->addIndex(
                $installer->getIdxName('magenest_productlabel_customer_group', ['customer_group_id']),
                ['customer_group_id']
            )
            ->addForeignKey(
                $installer->getFkName('magenest_productlabel_customer_group', 'rule_id', 'magenest_productlabel_rule', 'id'),
                'rule_id',
                $installer->getTable('magenest_productlabel_rule'),
                'id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'magenest_productlabel_customer_group',
                    'customer_group_id',
                    'customer_group',
                    'customer_group_id'
                ),
                'customer_group_id',
                $installer->getTable('customer_group'),
                'customer_group_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Product Label Rules To Customer Groups Relations');

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
