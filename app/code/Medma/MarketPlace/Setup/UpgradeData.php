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
 
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetupFactory;
  
class UpgradeData implements UpgradeDataInterface
{
    
    protected $dataFactory;

    protected $productFactory;
    
    public function __construct(
        \Medma\MarketPlace\Model\Configuration\DataFactory $dataFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\App\State $state,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->dataFactory = $dataFactory;
        $state->setAreaCode('adminhtml');
        $this->productFactory = $productFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }
 
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $data = [
                'group' => 'Email',
                'code' => 'notify_new_order_email',
                'name' => 'notify_new_order_email',
                'type' => 'select',
                'label' => 'Notify New Order Email to Me',
                'title' => 'Notify New Order Email to Me',
                'source_model' => 'adminhtml/system_config_source_yesno',
                    ];
 
            $post = $this->dataFactory->create();
 
            $post->addData($data)->save();
        }
        if(version_compare($context->getVersion(), '100.0.5', '<'))
        {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    'shipping_cost',
                    [
                    'group'=> 'General',
                    'type'=>'int',
                    'backend'=>'',
                    'frontend'=>'',
                    'label'=>'Shipping Cost',
                    'input'=>'text',
                    'class'=>'',
                    'source'=> '',
                    'global'=>\Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible'=>true,
                    'required'=>false,
                    'user_defined'=>true,
                    'default'=>'',
                    'searchable'=>false,
                    'filterable'=>false,
                    'comparable'=>false,
                    'visible_on_front'=>false,
                    'used_in_product_listing'=>false,
                    'unique'=>false,
                    'apply_to'=>'simple,configurable,virtual,bundle,downloadable,grouped'
                    ]
                );
        }
        $setup->endSetup();
    }
}
