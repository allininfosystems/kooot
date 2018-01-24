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
 
namespace Medma\MarketPlace\Controller\Adminhtml\Prooftype;
 
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    
    /**
     * @var \Medma\MarketPlace\Model\ProoftypeFactory
     */
    protected $prooftypeFactory;
  
 
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Medma\MarketPlace\Model\ProoftypeFactory $prooftypeFactory
     */
   
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Medma\MarketPlace\Model\ProoftypeFactory $prooftypeFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->prooftypeFactory = $prooftypeFactory;

        parent::__construct($context);
    }
 
    public function execute()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $testModel = $this->prooftypeFactory->create();
                $testModel->setId($this->getRequest()->getParam('id'))->delete();
                $this->_redirect('*/*/index');
                $this->messageManager->addSuccess('Data has been deleted successfully.');
            } catch (\Magento\Framework\Validator\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect($this->_redirect->getRefererUrl());
                return;
            }
        }
    }
    
    /**
     * Check Manage Proof Types Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Medma_MarketPlace::manage_verification_documents');
    }
}
