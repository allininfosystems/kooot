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
 
class Massdelete extends \Magento\Backend\App\Action
{
    protected $filter;
    protected $prooftypeFactory;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Medma\MarketPlace\Model\ResourceModel\Prooftype\CollectionFactory $collectionFactory,
        \Medma\MarketPlace\Model\ProoftypeFactory $prooftypeFactory
    ) {
        $this->filter = $filter;
        $this->prooftypeFactory = $prooftypeFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }
 
    public function execute()
    {

        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $feedbackDeleted = 0;
        foreach ($collection->getItems() as $feedback) {
            $this->prooftypeFactory->create()->load($feedback['entity_id'])->delete();
            $feedbackDeleted++;
        }
        $this->_redirect('admin_marketplace/prooftype/index');
        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $feedbackDeleted)
        );
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
