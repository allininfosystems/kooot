<?php

namespace Medma\MarketPlace\Block\Profile;

class VendorProfile extends \Magento\Framework\View\Element\Template
{
    
    protected $userFactory;
    protected $profileFactory;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\User\Model\UserFactory $userFactory,
        \Medma\MarketPlace\Model\ProfileFactory $profileFactory
    ) {
        $this->userFactory = $userFactory;
        $this->profileFactory = $profileFactory;
         parent::__construct($context);
    }
    public function getVendorList()
    {

        $profileModel = $this->profileFactory->create();
        $profileCollection = $profileModel->getCollection();

        return $profileCollection;
    }
        
    public function getVendorStatus($user_id)
    {
         $userModel = $this->userFactory->create();
         $item = $userModel->load($user_id);
         return $item->getData();
    }
        
    public function getVendorProfileUrl($vendorId)
    {
        return $this->getUrl('marketplace/vendor/profile', ['id' => $vendorId]);
    }
}
