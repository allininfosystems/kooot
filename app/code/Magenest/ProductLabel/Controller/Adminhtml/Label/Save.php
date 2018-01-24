<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ProductLabel
 * @author   ThaoPV <thaopw@gmail.com>
 */
namespace Magenest\ProductLabel\Controller\Adminhtml\Label;

use Magento\Backend\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;

/**
 * Class Save
 *
 * @package Magenest\ProductLabel\Controller\Adminhtml\Label
 */
class Save extends Action
{
    /**
     * Init
     *
     * @param Context         $context
     * @param Filesystem      $filesystem
     * @param UploaderFactory $fileUploaderFactory
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        UploaderFactory $fileUploaderFactory
    ) {
        $this->_filesystem          = $filesystem;
        $this->_fileUploaderFactory = $fileUploaderFactory;

        parent::__construct($context);
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Magenest\ProductLabel\Model\Label');

            if (!isset($data['category_image'])) {
                $data['category_image'] = [];
            }
            if (!isset($data['product_image'])) {
                $data['product_image'] = [];
            }

            $data['category_image'] = $this->saveImage($data['category_image'], 'category_image');
            $data['product_image']= $this->saveImage($data['product_image'], 'product_image');

            if (!empty($data['id'])) {
                $model->load($data['id']);
                if ($data['id'] != $model->getId()) {
                    throw new LocalizedException(__('Wrong label rule.'));
                }
            }

            $data['conditions'] = $data['rule']['conditions'];
            unset($data['rule']);

            $model->loadPost($data);
            $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($model->getData());
            try {
                $model->save();
                $this->messageManager->addSuccess(__('Rules has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError($e, __('Something went wrong while saving the label.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }


    /**
     * Save Image
     *
     * @param  $data
     * @param  $fileId
     * @return null|string
     */
    public function saveImage($data, $fileId)
    {
        if (!empty($data['delete']) && !empty($data['value'])) {
            $path = $this->_filesystem->getDirectoryRead(
                DirectoryList::MEDIA
            );
            if ($path->isFile($data['value'])) {
                $this->_filesystem->getDirectoryWrite(
                    DirectoryList::MEDIA
                )->delete($data['value']);
            }
        }

        $path = $this->_filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(
            'productlabel/rules/sale/'
        );
        try {
            /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
            $uploader = $this->_fileUploaderFactory->create(['fileId' => $fileId]);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(false);
            $result = $uploader->save($path);

            if (is_array($result) && !empty($result['name'])) {
                return 'productlabel/rules/sale/'.$result['name'];
            }
        } catch (\Exception $e) {
            if ($e->getCode() != Uploader::TMP_NAME_EMPTY) {
                $this->messageManager->addError($e);
            }
        }

        if (!empty($data)) {
            return $data['value'];
        }

        return null;
    }


    /**
     * ACL
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_ProductLabel::label');

    }
}
