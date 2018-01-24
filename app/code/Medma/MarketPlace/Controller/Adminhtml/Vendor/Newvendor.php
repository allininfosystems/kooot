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
 
use Magento\Framework\Controller\ResultFactory;
 
class Newvendor extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    protected $_coreRegistry;
 
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $_coreRegistry,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Medma\MarketPlace\Helper\Data $vendorHelper
    ) {
    
         $this->_adminSession = $adminSession;
        $this->vendorHelper = $vendorHelper;
         $this->_coreRegistry = $_coreRegistry;
        parent::__construct($context);
    }
    
    
    /**
     * @method :
     * @param  :
     * @return :
     */
 
    /**
     * Grid List page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
    

        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->_objectManager->create('Magento\User\Model\User');
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            if (!$rowData->getId()) {
                //echo $rowData->getId();
                $this->messageManager->addError(__('row data no longer exist.'));
                $this->_redirect('admin_marketplace/vendor/index');
                return;
            }
        }
        
        
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $rowData->setData($data);
        }
        
        
        
 
      //  $this->_coreRegistry->register('medma_marketplace_profile', $rowData);
        
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        //$title = $rowId ? __('Edit Type').$rowTitle : __('Add New Type');
        $resultPage->getConfig()->getTitle()->prepend('Vendor Information');
        return $resultPage;
    }
 
    /**
     * Check Manage Vendors Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
