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

namespace Medma\MarketPlace\Block;

use Medma\MarketPlace\Model\ProfileFactory;
 
class Favourite extends \Magento\Framework\View\Element\Template
{
   /**
    * @var Medma\MarketPlace\Model\ProfileFactory
    */
    protected $profile;
    
   /**
    * @var Medma\MarketPlace\Helper\Data
    */
    protected $marketHelper;

   /**
    * @var \Magento\User\Model\UserFactory
    */
    protected $adminuser;

   /**
    * @param \Magento\Catalog\Block\Product\Context $context
    * @param \Medma\MarketPlace\Model\ProfileFactory $profile
    * @param \Magento\Framework\Registry $registry
    * @param \Magento\User\Model\UserFactory $adminuser
    * @param \Medma\MarketPlace\Helper\Data $marketHelper
    */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        ProfileFactory $profile,
        \Magento\User\Model\UserFactory $adminuser,
        \Medma\MarketPlace\Helper\Data $marketHelper
    ) {
    
        $this->adminuser = $adminuser;
        $this->profile = $profile;
        $this->marketHelper = $marketHelper;
        parent::__construct($context);
    }
    
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getFavouriteVendors()
    {
        $profilemodel = $this->profile->create();
        $profileCollection = $profilemodel->getCollection();
        $customerId = $this->marketHelper->getLoggedInUser();
        if ($customerId) {
            $profiles = [];
            foreach ($profileCollection as $profile) {
                $favorites = $profile->getFavourites();
                if (!is_null($favorites) && !empty($favorites)) {
                    $favorites = json_decode($favorites, true);
                    if (in_array($customerId, $favorites)) {
                        $profiles[] = $profile->getId();
                    }
                }
            }
            
            $userIds = $this->adminuser->create()->getCollection()
                        ->addFieldToFilter('is_active', 1)
                        ->getAllIds();

            $profileCollection = $profilemodel->getCollection()
                                          ->addFieldToFilter('entity_id', ['in' => $profiles])
                                          ->addFieldToFilter('user_id', ['in' => $userIds]);
            return $profileCollection;
        }
        return ;
    }
    
    public function getUserObject($userId)
    {
        return $this->adminuser->create()->load($userId);
    }
    
    public function getCountryName($code)
    {
        return $this->marketHelper->getCountryName($code);
    }
        
    public function getMessage($vendorInfo, $userObject)
    {
        $message = $vendorInfo->getMessage();
        if (trim($message) != '') {
            return $message;
        } else {
            return (__('Based in ') .
                  $this->getCountryName($vendorInfo->getCountry()) . ' ' .
                  $vendorInfo->getShopName() . ' has been member since ' .
                  date("M j, Y", strtotime($userObject->getCreated())));
        }
    }
}
