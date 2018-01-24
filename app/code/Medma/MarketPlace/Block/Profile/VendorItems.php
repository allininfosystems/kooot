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

namespace Medma\MarketPlace\Block\Profile;

class VendorItems extends \Magento\Catalog\Block\Product\ListProduct
{
     
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $layer = $this->getLayer();
            
            /* @var $layer \Magento\Catalog\Model\Layer */
            if ($this->getShowRootCategory()) {
                $this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if ($this->_coreRegistry->registry('product')) {
            // get collection of categories this product is associated with
                $categories = $this->_coreRegistry->registry('product')
                    ->getCategoryCollection()->setPage(1, 1)
                    ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                try {
                    $category = $this->categoryRepository->get($this->getCategoryId());
                } catch (\Exception $e) {
                    $category = null;
                }

                if ($category) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                }
            }
            
            $this->_productCollection = $layer->getProductCollection();
            
            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());
            
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
            $profilemodel = $objectManager->create('Medma\MarketPlace\Model\Profile')->setWebsiteId(1);
            
            $profileCollection = $profilemodel->load($this->getRequest()->getParam('id'))->getData();
            
            //$adminusermodel = $this->adminuser->create();
            $adminusermodel = $objectManager->create('Magento\User\Model\User')->setWebsiteId(1);
            
            $userCollection = $adminusermodel->getCollection()->addFieldToFilter('is_active', 1);
                
            if (count($profileCollection) != 0) {
                $userCollection->addFieldToFilter('user_id', $profileCollection['user_id']);
            }
                
            //$userIds = $userCollection->getAllIds();
            $this->_productCollection->clear();
            $this->_productCollection->addAttributeToSelect('vendor')
                ->addAttributeToFilter('approved', 1)
                ->addAttributeToFilter('vendor',$profileCollection['user_id']);
            
            if ($this->getRequest()->getRouteName() == 'marketplace' &&
                $this->getRequest()->getControllerName() == 'vendor' &&
                $this->getRequest()->getActionName() == 'profile') {
                $profileId = $this->getRequest()->getParam('id');
                
                if (isset($profileId)) {
                    $vendorId = $profilemodel->load($profileId)->getUserId();
                
                    $this->_productCollection->addAttributeToSelect('*')
                        ->addAttributeToFilter('approved', 1);
                        // ->addAttributeToFilter('vendor', $vendorId);
                }
            }

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }
        }

        return $this->_productCollection;
    }
    
    public function getVendorName($user_id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->create('Medma\MarketPlace\Helper\Data')->getProfile($user_id);
        $user = $objectManager->create('Medma\MarketPlace\Helper\Data')->getUser($helper->getUserId());
        return $user;
    }
    public function getVendorId($user_id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->create('Medma\MarketPlace\Helper\Data')->getProfile($user_id);
        return $helper;
    }
    public function getVendorProfileUrl($vendorId)
    {
        return $this->getUrl('marketplace/vendor/profile', ['id' => $vendorId]);
    }
}
