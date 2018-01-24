<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Controller\Adminhtml\Actions;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magmodules\GoogleShopping\Model\Generate as GenerateModel;
use Magmodules\GoogleShopping\Helper\General as GeneralHelper;
use Psr\Log\LoggerInterface;

class Generate extends Action
{

    private $generate;
    private $logger;
    private $generalHelper;

    /**
     * Generate constructor.
     *
     * @param Context         $context
     * @param GenerateModel   $generateModel
     * @param GeneralHelper   $generalHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        GenerateModel $generateModel,
        GeneralHelper $generalHelper,
        LoggerInterface $logger
    ) {
        $this->generate = $generateModel;
        $this->logger = $logger;
        $this->generalHelper = $generalHelper;
        parent::__construct($context);
    }

    /**
     * Execute function for generation of the GoogleShopping feed in admin.
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store_id');

        if (!$this->generalHelper->getEnabled($storeId)) {
            $errorMsg = __('Please enable the extension before generating the feed.');
            $this->messageManager->addError($errorMsg);
        } else {
            try {
                $result = $this->generate->generateByStore($storeId);
                $this->messageManager->addSuccess(
                    __('Successfully generated a feed with %1 product(s).', $result['qty'])
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t generate the feed right now.' . $e));
                $this->logger->critical($e);
            }
        }
        $this->_redirect('adminhtml/system_config/edit/section/magmodules_googleshopping');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magmodules_GoogleShopping::config');
    }
}
