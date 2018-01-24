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
 
namespace Medma\MarketPlace\Block\Adminhtml;

class Transaction extends \Magento\Backend\Block\Template
{

    protected $coreRegistry;
    protected $adminSession;

    protected $_template = 'marketplace/vendor/transaction.phtml';
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Medma\MarketPlace\Helper\Data $vendorHelper,
        \Magento\Backend\Model\Auth\Session $adminSession
    ) {
    
        $this->coreRegistry = $coreRegistry;
        $this->adminSession = $adminSession;
        $this->vendorHelper = $vendorHelper;
        parent::__construct($context);
    }
    public function getHeaderText()
    {
        
        if ($this->coreRegistry->registry('vendor_user') && $this->coreRegistry->registry('vendor_user')->getId()) {
            return __("Balance sheet");
        }
    }
    public function isToShowButtons()
    {
        $roleId = $this->vendorHelper->getConfig('general', 'vendor_role');
        
        // $role = Mage::getModel('admin/roles')->load($roleId);

        $current_user = $this->adminSession->getUser();

        if ($current_user->getRole()->getRoleId() == $roleId) {
            return false;
        }
        return true;
    }
    public function getBackUrl()
    {
        return $this->getUrl('admin_marketplace/vendor/index');
    }

    public function getAddUrl()
    {
        return $this->getUrl('*/*/new', ['vendor_id' => $this->getRequest()->getParam('id')]);
    }
}
