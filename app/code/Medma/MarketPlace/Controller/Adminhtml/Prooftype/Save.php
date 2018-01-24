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
 
class Save extends \Magento\Backend\App\Action
{
    protected $prooftypeFactory;
     
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Medma\MarketPlace\Model\ProoftypeFactory $prooftypeFactory
    ) {
        $this->prooftypeFactory = $prooftypeFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        
        $prooftypemodel=$this->prooftypeFactory->create();
        if (array_key_exists('entity_id', $data)) {
            $prooftypemodel->load($data['entity_id'])->setName($data['name'])->setStatus($data['status'])->save();
            
            $this->messageManager->addSuccess(
                __('Data updated successfully.')
            );
            
            $this->_redirect('*/*/index');
        } else {
            $prooftypemodel->setName($data['name'])->setStatus($data['status'])->save();
            $this->messageManager->addSuccess(
                __('Data saved successfully.')
            );
            $this->_redirect('*/*/index');
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
