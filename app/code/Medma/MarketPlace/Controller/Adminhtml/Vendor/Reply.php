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

class Reply extends \Magento\Backend\App\Action
{
  
  /**
   * @var \Magento\Framework\View\Result\PageFactory
   */
    protected $_resultPageFactory;
    protected $detailfactory;
    protected $messagefactory;
    protected $timezoneInterface;
 
 
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
   
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Medma\MarketPlace\Model\DetailFactory $detailfactory,
        \Medma\MarketPlace\Model\MessageFactory $messagefactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->detailfactory = $detailfactory;
        $this->messagefactory = $messagefactory;
        $this->timezoneInterface = $timezoneInterface;
        parent::__construct($context);
    }
    /**
     * Vendor Profile page
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $created_at = $this->timezoneInterface->date()->format('m/d/y H:i:s');
        $params = $this->getRequest()->getParams();
        //$customer = $this->detailfactory->create()->getCollection()->addFieldToFilter('entity_id',$id)->getFirstItem();
        //$msgid = $customer->getId();
        $messagefactory = $this->messagefactory->create();
        $messagefactory->setMsgId($params['id']);
        $messagefactory->setType('vendor');
        $messagefactory->setContent($params['reply']);
        $messagefactory->setCreatedAt($created_at);
        $messagefactory->save();
        //$this->_redirect('*/*/index');
        //$this->messageManager->addSuccess(__('Your Message has been sent successfully'));
    }
}
