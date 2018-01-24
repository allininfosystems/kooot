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
 
class Sendmsg extends \Magento\Framework\View\Element\Template
{
    protected $userfactory;
    protected $profilefactory;
    protected $messagefactory;
    protected $detailfactory;
    protected $customerSession;
    protected $adminSession;
    
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\User\Model\UserFactory $userfactory,
        \Medma\MarketPlace\Model\ProfileFactory $profilefactory,
        \Medma\MarketPlace\Model\MessageFactory $messagefactory,
        \Medma\MarketPlace\Model\DetailFactory $detailfactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\User\Model\UserFactory $adminSession
    ) {
        $this->userfactory = $userfactory;
        $this->profilefactory = $profilefactory;
        $this->messagefactory = $messagefactory;
        $this->detailfactory = $detailfactory;
        $this->customerSession = $customerSession;
        $this->adminSession = $adminSession;
        parent::__construct($context);
    }
    public function getUser($userid)
    {
        return $this->userfactory->create()->load($userid);
    }
    public function getProfile($userid)
    {
        return $this->profilefactory->create()->load($userid);
    }
    public function getCollection()
    {
        $customerid = $this->customerSession->getCustomer()->getId();
        $msg_id=[];
        if ($customerid) {
            $msg_id[] = $this->detailfactory->create()->getCollection()->addFieldToFilter('customer_id', $customerid)->getAllIds();
            if(count($msg_id[0]))
            {
                return $this->messagefactory->create()->getCollection()->addFieldToFilter('msg_id', [$msg_id]);
            }
            else
            {
                return 'There are no messages';
            }

        } else {

            return 'There are no messages';
        }
    }
    public function getVendorName($msg_id)
    {
    
        $vendorid = $this->detailfactory->create()->load($msg_id)->getVendorId();
        $user_id = $this->profilefactory->create()->load($vendorid)->getUserId();
        return $this->adminSession->create()->load($user_id)->getName();
    }
}
