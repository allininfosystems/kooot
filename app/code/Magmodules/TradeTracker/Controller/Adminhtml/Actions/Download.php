<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\TradeTracker\Controller\Adminhtml\Actions;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magmodules\TradeTracker\Helper\Feed as FeedHelper;

/**
 * Class Download
 *
 * @package Magmodules\TradeTracker\Controller\Adminhtml\Actions
 */
class Download extends Action
{

    /**
     * @var FeedHelper
     */
    private $feedHelper;
    /**
     * @var FileFactory
     */
    private $fileFactory;
    /**
     * @var RawFactory
     */
    private $resultRawFactory;

    /**
     * Download constructor.
     *
     * @param Context       $context
     * @param RawFactory    $resultRawFactory
     * @param FileFactory   $fileFactory
     * @param FeedHelper    $feedHelper
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        FileFactory $fileFactory,
        FeedHelper $feedHelper,
        DirectoryList $directoryList
    ) {
        $this->feedHelper = $feedHelper;
        $this->fileFactory = $fileFactory;
        $this->resultRawFactory = $resultRawFactory;
        parent::__construct($context);
    }

    /**
     * Execute function for download of the TradeTracker feed in admin.
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store_id');
        $feed = $this->feedHelper->getFeedLocation($storeId);
        if (!empty($feed['full_path']) && file_exists($feed['full_path'])) {
            $this->fileFactory->create(
                basename($feed['full_path']),
                [
                    'type'  => 'filename',
                    'value' => 'tradetracker/' . basename($feed['full_path']),
                    'rm'    => false,
                ],
                DirectoryList::MEDIA,
                'application/octet-stream',
                null
            );
            $resultRaw = $this->resultRawFactory->create();
            return $resultRaw;
        } else {
            $this->messageManager->addError(__('File not found, please generate new feed.'));
            $this->_redirect('adminhtml/system_config/edit/section/magmodules_tradetracker');
        }
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magmodules_TradeTracker::config');
    }
}
