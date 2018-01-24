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
 
namespace Medma\MarketPlace\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;

class ConfigObserver implements ObserverInterface
{
    protected $productFactory;
    protected $scopeConfig; 
    protected $resourceConfig;
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
       \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
       \Magento\Framework\App\Config\ConfigResource\ConfigInterface $resourceConfig ) {
        $this->resourceConfig = $resourceConfig;
        $this->productFactory = $productFactory;
        $this->scopeConfig = $scopeConfig;
    }
 

    public function execute(Observer $observer)
    {
        $first_run =  $this->scopeConfig->getValue('productsearch/general/enabled_first',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if($first_run == 0)
         {   
         $productCollections = $this->productFactory->create()->getCollection();
            foreach ($productCollections as $productcollection) {
                $product = $this->productFactory->create()->load($productcollection->getId());
                $prod_name = $product->getName();
                $prod_namewithoutspace = str_replace(' ', '', $prod_name);
                $prod_namewithup = strtoupper($prod_namewithoutspace);
                $product->setNameKeyword($prod_namewithup);
                $product->save();
                $this->resourceConfig->saveConfig('productsearch/general/enabled_first',1,\Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            }
         }   
        
    }
}
