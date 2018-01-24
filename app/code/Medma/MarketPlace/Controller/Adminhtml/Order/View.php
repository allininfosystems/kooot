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

namespace Medma\MarketPlace\Controller\Adminhtml\Order;
 
class View extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    protected $adminSession;
    protected $vendorHelper;
    protected $coreregistry;
    protected $orderFactory;
 
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
   
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Framework\Registry $coreregistry,
        \Medma\MarketPlace\Helper\Data $vendorHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_adminSession = $adminSession;
        $this->coreregistry = $coreregistry;
        $this->vendorHelper = $vendorHelper;
        $this->orderFactory = $orderFactory;
        parent::__construct($context);
    }
 
    public function execute()
    {
           $id = $this->getRequest()->getParam('order_id');
        if ($id != 0) {
            $data = $this->_adminSession->getFormData(true);
            $this->coreregistry->register('current_order', $this->orderFactory->create()->load($id));

            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } else {
            $this->messageManager->addSuccess(__('Item does not exist.'));
            $this->_redirect('*/*/');
        }
    }
}
