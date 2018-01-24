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
 
namespace Medma\MarketPlace\Ui\DataProvider\Product;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * Class ProductDataProvider
 */
class VendorProductDataProvider extends \Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider
{
    public function getData()
    {
        
         $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
         $roleId = $objectManager->get('Medma\MarketPlace\Helper\Data')->getConfig('general', 'vendor_role');
         $current_user =$objectManager->get('Magento\Backend\Model\Auth\Session')->getUser();
        if ($current_user->getRole()->getRoleId() != $roleId) {
            if (!$this->getCollection()->isLoaded()) {
                $this->getCollection()->load();
            }
        } else {
            if (!$this->getCollection()->isLoaded()) {
                $this->getCollection()->addFieldToFilter('vendor', $current_user->getId())->load();
            }
        }
        
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        $items = $this->getCollection()->toArray();

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items),
        ];
    }
}
