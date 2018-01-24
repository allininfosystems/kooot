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
 
use Magento\Framework\Controller\ResultFactory;
 
class View extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    protected $_coreRegistry;
 
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
    
        parent::__construct($context);
    }
    
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        //$title = $rowId ? __('Edit Type').$rowTitle : __('Add New Type');
        $resultPage->getConfig()->getTitle()->prepend('Messages');
        return $resultPage;
    }
 
    /**
     * Check Manage Vendors Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
