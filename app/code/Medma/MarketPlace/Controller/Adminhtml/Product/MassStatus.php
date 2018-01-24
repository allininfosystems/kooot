<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Medma\MarketPlace\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class MassStatus extends \Magento\Catalog\Controller\Adminhtml\Product\MassStatus
{

    /**
     * Update product(s) status action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $proIds = $collection->getAllIds();
        $productLoader = $this->_objectManager->get('Magento\Catalog\Model\Product');
        $vendorId = $this->_objectManager->get('\Medma\MarketPlace\Helper\Data')->getLoggedInAdminUserId();
        $userInfo = $this->_objectManager->get('\Magento\User\Model\User')->load($vendorId);
        $roleData = $userInfo->getRole()->getData();
        if($roleData['role_name']=="VENDORS")
        {
            foreach($proIds as $productId)
            {
                if($productLoader->load($productId)->getVendor()==$vendorId)
                {
                    $productIds[] = $productId;
                }
            }
        }
        else
        {
           $productIds = $collection->getAllIds();
        }
        
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $status = (int) $this->getRequest()->getParam('status');
        $filters = (array)$this->getRequest()->getParam('filters', []);

        if (isset($filters['store_id'])) {
            $storeId = (int)$filters['store_id'];
        }

        try {
            $this->_validateMassStatus($productIds, $status);
            $this->_objectManager->get('Magento\Catalog\Model\Product\Action')
                ->updateAttributes($productIds, ['status' => $status], $storeId);
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been updated.', count($productIds)));
            $this->_productPriceIndexerProcessor->reindexList($productIds);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addException($e, __('Something went wrong while updating the product(s) status.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('catalog/*/', ['store' => $storeId]);
    }
}
