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
 
namespace Medma\MarketPlace\Controller\Adminhtml\Export;

use Magento\Framework\Controller\ResultFactory;
use Magento\ImportExport\Controller\Adminhtml\Export as ExportController;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\ImportExport\Model\Export as ExportModel;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;

class Export extends \Magento\ImportExport\Controller\Adminhtml\Export\Export
{
    public function execute()
    {
        if ($this->getRequest()->getPost(ExportModel::FILTER_ELEMENT_GROUP)) {
            try {
                /** @var $model \Magento\ImportExport\Model\Export */
                $model = $this->_objectManager->create('Magento\ImportExport\Model\Export');
                $filterData = $this->getRequest()->getParams();
                if(isset($filterData['vendor_id']) && $filterData['vendor_id'] !='')
                {
                    $filterData['export_filter']['vendor'] = $filterData['vendor_id'];
                }
                $model->setData($filterData);
                return $this->fileFactory->create(
                    $model->getFileName(),
                    $model->export(),
                    DirectoryList::VAR_DIR,
                    $model->getContentType()
                    );
                } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->messageManager->addError(__('Please correct the data sent value.'));
            }
        } else {
            $this->messageManager->addError(__('Please correct the data sent value.'));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('adminhtml/*/index');
        return $resultRedirect;
    }
}
