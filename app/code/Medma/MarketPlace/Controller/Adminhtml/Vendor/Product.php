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
 
namespace Medma\MarketPlace\Controller\Adminhtml\Vendor;
 
class Product extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    protected $adminSession;
    protected $vendorHelper;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Medma\MarketPlace\Helper\Data $vendorHelper
    ) {
    
        parent::__construct($context);
        $this->_adminSession = $adminSession;
        $this->vendorHelper = $vendorHelper;
        $this->_resultPageFactory = $resultPageFactory;
    }
 
    /**
     * Grid List page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        //$vendor_id = $this->getRequest()->getParam('id');
        //echo "test";
        $roleId = $this->vendorHelper->getConfig('general', 'vendor_role');

        $current_user = $this->_adminSession->getUser();
        
        if ($current_user->getRole()->getRoleId() == $roleId) {
            $resultPage = $this->_resultPageFactory->create();
            $resultPage->setActiveMenu('Medma_MarketPlace::vendor');
            $resultPage->getConfig()->getTitle()->prepend(__('Manage Products'));
        } else {
            $this->_forward('empty');
            return;
        }

    
          return $resultPage;
    }
 
    /**
     * Check Manage Vendors Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Medma_MarketPlace::vendor');
    }
}
