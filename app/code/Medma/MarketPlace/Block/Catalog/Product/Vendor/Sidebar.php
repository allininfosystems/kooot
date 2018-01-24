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
 
namespace Medma\MarketPlace\Block\Catalog\Product\Vendor;

use Medma\MarketPlace\Model\ProfileFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Catalog\Model\ProductFactory;
 
class Sidebar extends \Magento\Framework\View\Element\Template
{
   
    protected $profile;
    protected $messageManager;
    protected $ModelUser;
    protected $marketHelper;
    protected $registry;
    protected $ProductFactory;
    
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\User\Model\UserFactory $adminuser,
        \Medma\MarketPlace\Helper\Data $marketHelper,
        ProfileFactory $profile,
        ManagerInterface $messageManager,
        ProductFactory $ProductFactory
    ) {
        
         $this->profile = $profile;
         $this->messageManager = $messageManager;
         $this->marketHelper = $marketHelper;
         $this->ModelUser = $adminuser;
         $this->registry = $context->getRegistry();
         $this->ProductFactory = $ProductFactory;
            
         parent::__construct($context);
    }
        
    public function getProduct()
    {
        if (!$this->registry->registry('product') && $this->getProductId()) {
            $product = $this->ProductFactory->create()->load($this->getProductId());
            //$product = $this->ProductFactory->create()->load(2);
            $this->registry->register('product', $product);
        }
        return $this->registry->registry('product');
    }

    public function getVendorInfo()
    {
        $userId = $this->getProduct()->getVendor();
        $vendorProfileCollection = $this->profile->create()->getCollection()->addFieldToFilter('user_id', $userId)->getData();
        
        $userCollection = $this->ModelUser->create()->getCollection()->addFieldToFilter('user_id', $userId)->getData();
            
        if (!empty($vendorProfileCollection) && !empty($userCollection)) {
            return $vendorProfileCollection['0'];

        } else {
            return null;
        }
    }

    public function getVendorProfileUrl($vendorId)
    {
        return $this->getUrl('marketplace/vendor/profile', ['id' => $vendorId]);
    }

    public function getAddFavouriteUrl($vendorId)
    {
        return $this->getUrl('marketplace/favourite/add', ['id' => $vendorId]);
    }

    public function getProductId()
    {
        return (int) $this->getRequest()->getParam('id');
    }
        
    public function getConfigData($group, $field)
    {
        return $this->marketHelper->getConfig($group, $field);
    }
        
    public function getShopInfoDisplay()
    {
        return $this->marketHelper->getConfig("general", "shop_info_display");
    }
}
