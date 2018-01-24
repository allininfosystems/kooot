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
 
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
 
class UpgradeSchema implements UpgradeSchemaInterface
{
 
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
 
        $installer->startSetup();
        
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('medma_marketplace_prooftype')
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Entity Id'
            )->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Name'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                25,
                [],
                'Status'
            )->setComment(
                'Marketplace Profile Table'
            );
                
            $installer->getConnection()->createTable($table);
        }
        
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('medma_marketplace_transaction')
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Entity Id'
            )->addColumn(
                'vendor_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                [],
                'Vendor id'
            )->addColumn(
                'transaction_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Transaction Date'
            )->addColumn(
                'order_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Order Number'
            )->addColumn(
                'information',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'information'
            )->addColumn(
                'amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                [],
                'amount'
            )->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'type'
            )->setComment(
                'Marketplace Transaction Table'
            );
            
            $installer->getConnection()->createTable($table);
        }
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('medma_marketplace_configuration')
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Entity Id'
            )->addColumn(
                'vendor_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                [],
                'Vendor id'
            )->addColumn(
                'value',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'value'
            )->setComment(
                'Marketplace Configuration Table'
            );
            
            $installer->getConnection()->createTable($table);
        }
        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('medma_marketplace_configuration_data')
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Entity Id'
            )->addColumn(
                'group',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Group'
            )
                ->addColumn(
                    'code',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Code'
                )
                ->addColumn(
                    'name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Name'
                )
                ->addColumn(
                    'group',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Group'
                )
                ->addColumn(
                    'type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'type'
                )
                ->addColumn(
                    'label',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Label'
                )
                ->addColumn(
                    'title',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Title'
                )
                ->addColumn(
                    'class',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Class'
                )
                ->addColumn(
                    'style',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Style'
                )
                ->addColumn(
                    'class',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Class'
                )
                ->addColumn(
                    'options',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Options'
                )
                ->addColumn(
                    'source_model',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Source Model'
                )
                ->addColumn(
                    'after_element_html',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'After Element Html'
                )
                ->addColumn(
                    'disable',
                    \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    null,
                    [],
                    'Disable'
                )
                ->addColumn(
                    'readonly',
                    \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    null,
                    [],
                    'Readonly'
                )
                ->addColumn(
                    'readonly',
                    \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    null,
                    [],
                    'Readonly'
                )->setComment(
                    'Marketplace Configuration Data Table'
                );
            
            $installer->getConnection()->createTable($table);
        }
        
        if (version_compare($context->getVersion(), '1.0.6', '<')) {
            $connection = $installer->getConnection();

            $column = [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => 11,
                'nullable' => false,
                'comment' => 'Seller Vacation Mode',
                'default' => '0'
            ];
            $connection->addColumn($installer->getTable('admin_user'), 'seller_vacation_mode', $column);
        }
        
        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('medma_marketplace_review_rating')
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Entity Id'
            )->addColumn(
                'vendor_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'Vendor Id'
            )
                ->addColumn(
                    'customer_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [],
                    'Customer Id'
                )
                ->addColumn(
                    'quality',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [],
                    'Quality'
                )
                ->addColumn(
                    'price',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [],
                    'Price'
                )
                ->addColumn(
                    'shipping',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [],
                    'Shipping'
                )
                ->addColumn(
                    'nickname',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Nickname'
                )
                ->addColumn(
                    'summary',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Summary'
                )
                ->addColumn(
                    'review',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    1000,
                    [],
                    'Review'
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Created At'
                )
                ->setComment(
                    'Marketplace Review Rating'
                );
            
            $installer->getConnection()->createTable($table);
            
            $table = $installer->getConnection()->newTable(
                $installer->getTable('medma_vendor_msgs')
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Entity Id'
            )->addColumn(
                'msg_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Msg Id'
            )
                ->addColumn(
                    'type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Type'
                )
                ->addColumn(
                    'content',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    1000,
                    [],
                    'Content'
                )->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Created At'
                )->setComment(
                    'Vendor Messaging'
                );
                
            $installer->getConnection()->createTable($table);
        }
        if(version_compare($context->getVersion(), '100.0.5', '<'))
        {
            $eavTable = $installer->getTable('medma_marketplace_profile');

            $columns = [
                'client_email_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Client email ID',
                ],
                'client_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Client ID',
                ],
                'client_secret' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Client Secret',
                ],
                'stripe_email_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Stripe Email ID',
                ],
                'stripe_secret_api_key' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Stripe Secret API Key',
                ],
                'stripe_publishable_api_key' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Stripe Publishable API Key',
                ],

            ];

            $connection = $installer->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->addColumn($eavTable, $name, $definition);
            }
                }
                

        $installer->endSetup();
    }
}
