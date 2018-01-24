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
 
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Eav\Setup\EavSetupFactory;
 
class InstallData implements InstallDataInterface
{
 
    protected $rolesFactory;
    protected $rulesFactory;
    protected $_resourceConfig;
    protected $eavSetupFactory;
 
    public function __construct(
        \Magento\Authorization\Model\RoleFactory $rolesFactory,
        \Magento\Authorization\Model\RulesFactory $rulesFactory,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->rolesFactory = $rolesFactory;
        $this->rulesFactory = $rulesFactory;
        $this->_resourceConfig = $resourceConfig;
        $this->eavSetupFactory = $eavSetupFactory;
    }
 
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $ROLE_NAME = "VENDORS" ;
        
        ##### CHECK WHETHER ROLE EXISTS #####
        
        $role=$this->rolesFactory->create();
        $collection = $role->getCollection()->addFieldToFilter('role_name', $ROLE_NAME);
        $role_count = $collection->getSize();
        
        if ($role_count==0) {
        ##### CREATE ROLE #####
            
            $role->setName($ROLE_NAME)
                    ->setRoleType(RoleGroup::ROLE_TYPE)
                    ->setPid(0)
                    ->setUserType(UserContextInterface::USER_TYPE_ADMIN);
            $role->save();
            
            $vendor_id = $role->getId();
            
            
            ##### Set RULE FOR ADDED ROLE #####
            
            /*$resource=[ 'Magento_Backend::admin',
						'Magento_Backend::convert',
						'Magento_Backend::myaccount',
						'Magento_Backend::system',
						'Magento_Catalog::catalog',
						'Magento_Catalog::catalog_inventory',
						'Magento_Catalog::products',
						'Magento_ImportExport::export',
						'Magento_ImportExport::import',
						'Medma_MarketPlace::pending_reviews',
						'Medma_MarketPlace::all_reviews',
						'Medma_MarketPlace::manage_ratings',
						'Medma_MarketPlace::manage_verification'
					  ]; */
            $resource=[ 'Magento_Backend::admin',
                        'Magento_Backend::convert',
                        'Magento_Backend::myaccount',
                        'Magento_Backend::system',
                        'Magento_Catalog::catalog',
                        'Magento_Catalog::catalog_inventory',
                        'Magento_Catalog::products',
                        'Magento_Backend::stores',
                        'Magento_Backend::stores_attributes',
                        'Magento_Catalog::attributes_attributes',
                        'Magento_ImportExport::export',
                        'Magento_ImportExport::import',
                        'Medma_MarketPlace::MarketplaceVendorMenu',
                        'Medma_MarketPlace::vendor_menus',
                        'Medma_MarketPlace::my_account_section',
                        'Medma_MarketPlace::orders',
                        'Medma_MarketPlace::transactions',
                        'Medma_MarketPlace::reviews',
                        'Medma_MarketPlace::vendor_configuration',
                        'Medma_MarketPlace::manage_messages',
                        'Magento_Backend::stores_attributes',
                        'Magento_Catalog::update_attributes'
                        ];
            $this->rulesFactory->create()->setRoleId($role->getId())->setResources($resource)->saveRel();
            
            ##### SET CONFIG DATA FOR VENDOR #####
            
            $path = 'marketplace/general/vendor_role';
            $value = $vendor_id;
            $scope = 'default';
            $scopeId = 0;
        
            $this->_resourceConfig->saveConfig($path, $value, $scope, $scopeId);
            
            ##### CREATE ATTRIBUTES - 'vendor' , 'approved' #####
            
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
  
        
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'vendor',
                [
                'group'=> 'General',
                'type'=>'int',
                'backend'=>'',
                'frontend'=>'',
                'label'=>'Vendor',
                'input'=>'select',
                'class'=>'',
                'source'=> 'Medma\MarketPlace\Model\Product\Attribute\Source\Vendor',
                'global'=>\Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible'=>true,
                'required'=>false,
                'user_defined'=>false,
                'default'=>'',
                'searchable'=>false,
                'filterable'=>false,
                'comparable'=>false,
                'visible_on_front'=>false,
                'used_in_product_listing'=>false,
                'unique'=>false,
                'apply_to'=>'simple,configurable,virtual,bundle,downloadable'
                ]
            );
       
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    'approved',
                    [
                    'group'=> 'General',
                    'type'=>'int',
                    'backend'=>'',
                    'frontend'=>'',
                    'label'=>'Approved',
                    'input'=>'select',
                    'class'=>'',
                    'source'=> 'Medma\MarketPlace\Model\System\Config\Source\Approved',
                    'global'=>\Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible'=>true,
                    'required'=>false,
                    'user_defined'=>false,
                    'default'=>'',
                    'searchable'=>false,
                    'filterable'=>false,
                    'comparable'=>false,
                    'visible_on_front'=>false,
                    'used_in_product_listing'=>false,
                    'unique'=>false,
                    'apply_to'=>'simple,configurable,virtual,bundle,downloadable'
                    ]
                );
              
           /**
            *
            * this attribute is of product search Module
            *
            */
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    'name_keyword',
                    [
                    'group'=> 'General',
                    'type'=>'varchar',
                    'backend'=>'',
                    'frontend'=>'',
                    'label'=>'Name Keyword',
                    'input'=>'text',
                    'class'=>'',
                    'source'=> '',
                    'global'=>\Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible'=>true,
                    'required'=>false,
                    'user_defined'=>false,
                    'default'=>'',
                    'searchable'=>true,
                    'filterable'=>false,
                    'comparable'=>false,
                    'visible_on_front'=>false,
                    'used_in_product_listing'=>false,
                    'unique'=>false,
                    'apply_to'=>'simple,configurable,virtual,bundle,downloadable'
                    ]
                );
        }
    }
}
    