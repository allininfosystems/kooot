<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\TradeTracker\Controller\Adminhtml\Actions;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magmodules\TradeTracker\Model\Feed as FeedModel;
use Magmodules\TradeTracker\Helper\General as GeneralHelper;

/**
 * Class Preview
 *
 * @package Magmodules\TradeTracker\Controller\Adminhtml\Actions
 */
class Preview extends Action
{

    /**
     * @var FeedModel
     */
    private $feedModel;
    /**
     * @var GeneralHelper
     */
    private $generalHelper;

    /**
     * Preview constructor.
     *
     * @param Context       $context
     * @param GeneralHelper $generalHelper
     * @param FeedModel     $feedModel
     */
    public function __construct(
        Context $context,
        GeneralHelper $generalHelper,
        FeedModel $feedModel
    ) {
        $this->feedModel = $feedModel;
        $this->generalHelper = $generalHelper;
        parent::__construct($context);
    }

    /**
     * Execute function for preview of the TradeTracker feed in admin.
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store_id');
        if (!$this->generalHelper->getEnabled($storeId)) {
            $errorMsg = __('Please enable the extension before generating the feed.');
            $this->messageManager->addError($errorMsg);
            $this->_redirect('adminhtml/system_config/edit/section/magmodules_tradetracker');
        } else {
            try {
                $page = $this->getRequest()->getParam('page', 1);
                $productId = $this->getRequest()->getParam('pid', []);
                $data = $this->getRequest()->getParam('data', 0);
                if ($result = $this->feedModel->generateByStore($storeId, 'preview', $productId, $page, $data)) {
                    $this->getResponse()->setHeader('Content-type', 'text/xml');
                    $this->getResponse()->setBody(file_get_contents($result['path']));
                } else {
                    $errorMsg = __('Unkown error.');
                    $this->messageManager->addError($errorMsg);
                    $this->_redirect('adminhtml/system_config/edit/section/magmodules_tradetracker');
                }
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('We can\'t generate the feed right now, please check error log')
                );
                $this->generalHelper->addTolog('Generate', $e->getMessage());
                $this->_redirect('adminhtml/system_config/edit/section/magmodules_tradetracker');
            }
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
