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
 
namespace Medma\MarketPlace\Controller\Adminhtml\Transaction;
 
use Magento\Framework\Controller\ResultFactory;

class NewAction extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    protected $adminSession;
    protected $vendorHelper;
    protected $profileFactory;
    protected $userFactory;
    protected $coreRegistry;
 
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
   
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Medma\MarketPlace\Helper\Data $vendorHelper,
        \Magento\User\Model\UserFactory $userFactory,
        \Medma\MarketPlace\Model\ProfileFactory $profileFactory,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->userFactory = $userFactory;
        $this->_adminSession = $adminSession;
        $this->vendorHelper = $vendorHelper;
        $this->profileFactory = $profileFactory;
        parent::__construct($context);
    }
 
    public function execute()
    {

         $vendor_id = $this->getRequest()->getParam('vendor_id');
         $testModel = $this->userFactory->create()->load($vendor_id);
         $this->coreRegistry->register('vendor_user', $testModel);
          
        $profileModel = $this->profileFactory->create()->getCollection()->addFieldToFilter('user_id', $vendor_id)->getFirstItem();
        $this->coreRegistry->register('vendor_profile', $profileModel);
          
         $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        //$title = $rowId ? __('Edit Type').$rowTitle : __('Add New Type');
        $resultPage->getConfig()->getTitle()->prepend('New Transaction');
        return $resultPage;
    }
}
