<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\TradeTracker\Controller\Adminhtml\Actions;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magmodules\TradeTracker\Model\Feed as FeedModel;
use Magmodules\TradeTracker\Helper\General as GeneralHelper;

/**
 * Class Generate
 *
 * @package Magmodules\TradeTracker\Controller\Adminhtml\Actions
 */
class Generate extends Action
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
     * Generate constructor.
     *
     * @param Context       $context
     * @param FeedModel     $feedModel
     * @param GeneralHelper $generalHelper
     */
    public function __construct(
        Context $context,
        FeedModel $feedModel,
        GeneralHelper $generalHelper
    ) {
        $this->feedModel = $feedModel;
        $this->generalHelper = $generalHelper;
        parent::__construct($context);
    }

    /**
     * Execute function for generation of the TradeTracker feed in admin.
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store_id');

        if (!$this->generalHelper->getEnabled($storeId)) {
            $errorMsg = __('Please enable the extension before generating the feed.');
            $this->messageManager->addError($errorMsg);
        } else {
            try {
                $result = $this->feedModel->generateByStore($storeId);
                $this->messageManager->addSuccess(
                    __('Successfully generated a feed with %1 product(s).', $result['qty'])
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addException(
                    $e,
                    __('We can\'t generate the feed right now, please check error log')
                );
                $this->generalHelper->addTolog('Generate', $e);
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('We can\'t generate the feed right now, please check error log')
                );
                $this->generalHelper->addTolog('Generate', $e);
            }
        }
        $this->_redirect('adminhtml/system_config/edit/section/magmodules_tradetracker');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magmodules_TradeTracker::config');
    }
}
