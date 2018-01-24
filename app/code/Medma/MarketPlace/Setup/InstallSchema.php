<?php
/**
 *
 * Copyright © 2016 Medma. All rights reserved.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 */
 
namespace Medma\MarketPlace\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'medma_marketplace_profile'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('medma_marketplace_profile')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'User Id'
        )->addColumn(
            'image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Image'
        )->addColumn(
            'admin_commission_percentage',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Admin Commission Percentage'
        )->addColumn(
            'total_admin_commission',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Total Admin Commission'
        )->addColumn(
            'total_vendor_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Total Vendor Amount'
        )->addColumn(
            'total_vendor_paid',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            [],
            'Total Vendor Paid'
        )->addColumn(
            'shop_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Shop Name'
        )->addColumn(
            'message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'message'
        )->addColumn(
            'contact_number',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Contact Number'
        )->addColumn(
            'country',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Country'
        )->addColumn(
            'favourites',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Favourites'
        )->addColumn(
            'proof_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'proof_type'
        )->addColumn(
            'varification_files',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Varification files'
        )->addColumn(
            'admin_commission_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '10,0',
            [],
            'Admin Commission Type'
        )->addColumn(
            'admin_commission_flat',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '10,0',
            [],
            'Admin Commission Flat'
        )->addColumn(
            'display_profile_frontend_for_admin',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '10,0',
            [],
            'Display profile frontend for admin'
        )->addColumn(
            'display_profile_frontend_for_vendor',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '10,0',
            [],
            'Display profile frontend for Vendor'
        )->addColumn(
            'sell_products_on_installment_for_admin',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '10,0',
            [],
            'Sell products on installment for admin'
        )->addColumn(
            'sell_products_on_installment_for_vendor',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '10,0',
            [],
            'Sell products on installment for Vendor'
        )->addColumn(
            'create_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Create Date'
        )->setComment(
            'Marketplace Profile Table'
        );
        $installer->getConnection()->createTable($table);
        
        $table = $installer->getConnection()->newTable(
            $installer->getTable('medma_msg_detail')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'vendor_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'User Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Customer Id'
        )->setComment(
            'Messages Detail'
        );
        $installer->getConnection()->createTable($table);
        /* order admin comission table */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('medma_order_admin_commission')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [ 'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0'],
            'Order Id'
        )->addColumn(
            'vendor_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Vendor Id'
        )->addColumn(
            'commission_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Commission Type'
        )->addColumn(
            'commission_flat',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Commission Flat'
        )->addColumn(
            'commission_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Commission Amount'
        )->addIndex(
            $installer->getIdxName(
                'medma_order_admin_commission',
                ['order_id']
            ),
            ['order_id']
        )->addForeignKey(
            $installer->getFkName('medma_order_admin_commission', 'order_id', 'sales_order', 'entity_id'),
            'order_id',
            $installer->getTable('sales_order'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Order admin commission'
        );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
