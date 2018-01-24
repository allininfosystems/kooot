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

use Magento\Authorization\Model\Acl\Role\User as RoleGroup;
 
class Search extends \Magento\Framework\View\Element\Template
{
    protected $profilefactory;
    protected $userfactory;
    protected $marketHelper;
    protected $rolesFactory;
   
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Medma\MarketPlace\Model\Profilefactory $profilefactory,
        \Magento\User\Model\UserFactory $userfactory,
        \Medma\MarketPlace\Helper\Data $marketHelper,
        \Magento\Authorization\Model\RoleFactory $rolesFactory
    ) {
        $this->profilefactory = $profilefactory;
        $this->userfactory = $userfactory;
        $this->marketHelper = $marketHelper;
        $this->rolesFactory = $rolesFactory;
        parent::__construct($context);
    }
    public function getVendorCollection()
    {
        $searchtext = $this->getRequest()->getParam('key');
        if ($searchtext) {
            return $usercollection = $this->userfactory->create()->getCollection()->addFieldToFilter('is_active', '1')
                                     ->addFieldToFilter('firstname', ['neq'=>'admin'])
                                     ->addFieldToFilter('firstname', ['like'=>'%'.$searchtext.'%']);
        }
    }
    public function getEmail($userId)
    {
        return $this->userfactory->create()->load($userId)->getEmail();
    }
    public function getProfile($userid)
    {
        return $this->profilefactory->create()->getCollection()->addFieldToFilter('user_id', $userid)->getFirstItem();
    }
}
