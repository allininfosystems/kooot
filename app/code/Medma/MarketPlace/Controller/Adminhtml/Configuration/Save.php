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
 
namespace Medma\MarketPlace\Controller\Adminhtml\Configuration;
 
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    protected $adminSession;
    protected $vendorHelper;
    protected $dataFactory;

 
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
   
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Medma\MarketPlace\Model\ConfigurationFactory $dataFactory,
        \Medma\MarketPlace\Helper\Data $vendorHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->adminSession = $adminSession;
        $this->vendorHelper = $vendorHelper;
        $this->dataFactory = $dataFactory;
        parent::__construct($context);
    }
 
    public function execute()
    {
        $userId = $this->getRequest()->getParam('id');
        $postData = $this->getRequest()->getPost();

        
        //unset($postData['form_key']);


        $current_user = $this->adminSession->getUser();

        if (!isset($userId)) {
            $userId = $current_user->getUserId();
        }

        $configDataCollection = $this->dataFactory->create()->getCollection()->addFieldToFilter('vendor_id', $userId);

    
        if (count($configDataCollection)) {
            $configDataObject = $configDataCollection->getFirstItem();
        
            $configDataObject->setValue(json_encode($postData))->save();
        } else {
            $this->dataFactory->create()
                ->setVendorId($userId)
                ->setValue(json_encode($postData))
                ->save();
        }
        $this->messageManager->addSuccess('configuration successfully saved');
        
        $roleId = $this->vendorHelper->getConfig('general', 'vendor_role');

        if ($current_user->getRole()->getRoleId() == $roleId) {
            $this->_redirect('*/*/index');
        } else {
            $this->_redirect('*/*/vendor', ['id' =>  $userId]);
        }
    }
}
