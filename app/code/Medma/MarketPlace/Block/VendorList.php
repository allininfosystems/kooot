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
 
class VendorList extends \Magento\Framework\View\Element\Template
{

   /**
    * @var \Medma\Productsearch\Helper\Data $marketHelper
    */
    protected $Helper;
   
   /**
    * @var \Magento\Framework\Registry $registry
    */
    protected $registry;
    protected $collectionfactory;
    protected $userfactory;
    protected $profilefactory;
    
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ProductFactory $collectionfactory,
        \Magento\User\Model\UserFactory $userfactory,
        \Medma\MarketPlace\Model\ProfileFactory $profilefactory,
        \Medma\MarketPlace\Helper\Data $Helper
    ) {
        $this->registry = $context->getRegistry();
        $this->userfactory = $userfactory;
        $this->profilefactory = $profilefactory;
        $this->Helper = $Helper;
        $this->collectionfactory = $collectionfactory;
        parent::__construct($context);
    }
    public function getProductCollection()
    {
        if ($this->registry->registry('product')) {
            $vendorId = $this->registry->registry('product')->getVendor();
            $name_keyword = $this->registry->registry('product')->getNameKeyword();
            $collection = $this->collectionfactory->create()->getCollection()
                        ->addAttributeToFilter('approved', 1)
                         ->addAttributeToFilter('name_keyword', $name_keyword)
                         ->setOrder('price', 'asc');
        }
        return $collection;
    }
    public function getProduct($id)
    {
        return $this->collectionfactory->create()->load($id);
    }
    public function getUser($userid)
    {
        return $this->userfactory->create()->load($userid);
    }
    public function getProfile($userid)
    {
        return $this->profilefactory->create()->getCollection()->addFieldToFilter('user_id', $userid)->getFirstItem();
    }
}
