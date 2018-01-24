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
 
class Delete extends \Magento\Backend\App\Action
{
    protected $_resultPageFactory;
    protected $productFactory;
    protected $collectionFactory;
    protected $userFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->productFactory = $productFactory;
        $this->userFactory = $userFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }
 
    public function execute()
    {

        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $this->deleteProducts($this->getRequest()->getParam('id'));
                $testModel = $this->userFactory->create();
                $testModel->setId($this->getRequest()->getParam('id'))->delete();
                $this->_redirect('*/*/index');
                $this->messageManager->addSuccess('Vendor has been deleted successfully.');
            } catch (\Magento\Framework\Validator\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect($this->_redirect->getRefererUrl());
                return;
            }
        }
    }
    public function deleteProducts($userId)
    {
        $productIds = $this->collectionFactory->create()->addAttributeToFilter('vendor', $userId);
        foreach ($productIds as $product => $id) {
            $product = $this->productFactory->create()->load($id->getId());
            try {
                $product->delete();
            } catch (\Magento\Framework\Validator\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect($this->_redirect->getRefererUrl());
                return;
            }
        }
    }
}
