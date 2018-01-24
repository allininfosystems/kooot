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
  
namespace Medma\MarketPlace\Block\Adminhtml\Transaction;

class Items extends \Magento\Backend\Block\Template
{

    protected $coreRegistry;
    protected $adminSession;
    protected $transactionFactory;
    protected $vendorHelper;

    protected $_template = 'marketplace/vendor/transaction/items.phtml';
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Medma\MarketPlace\Helper\Data $vendorHelper,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Medma\MarketPlace\Model\TransactionFactory $transactionFactory
    ) {
    
        $this->coreRegistry = $coreRegistry;
        $this->adminSession = $adminSession;
        $this->vendorHelper = $vendorHelper;
        $this->transactionFactory = $transactionFactory;
        parent::__construct($context);
    }

    public function getTransactions()
    {
        $roleId = $this->vendorHelper->getConfig('general', 'vendor_role');
        $current_user = $this->adminSession->getUser();
        if ($current_user->getRole()->getRoleId() == $roleId) {
            $vendor_id=$current_user->getId();
        } else {
            $vendor_id = $this->getRequest()->getParam('id');
        }
        
        return $this->transactionFactory->create()
          ->getCollection()
          ->addFieldToFilter('vendor_id', $vendor_id);
    }
}
