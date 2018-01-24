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

namespace Medma\MarketPlace\Controller\Vendor;

use Magento\Customer\Model\Session;

class Send extends \Magento\Framework\App\Action\Action
{
  
   
  /**
   * @var \Magento\Framework\Controller\Result\ForwardFactory
   */
    protected $_coreRegistry = null;
    
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    protected $customerSession;
    protected $detailfactory;
    protected $messagefactory;
    protected $timezoneInterface;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
     
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        Session $customerSession,
        \Medma\MarketPlace\Model\DetailFactory $detailfactory,
        \Medma\MarketPlace\Model\MessageFactory $messagefactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
    ) {
        $this->resultPageFactory  = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->detailfactory = $detailfactory;
        $this->messagefactory = $messagefactory;
        $this->timezoneInterface = $timezoneInterface;

        parent::__construct($context);
        $this->customerSession = $customerSession;
    }
  
    /**
     * Vendor Profile page
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $created_at = $this->timezoneInterface->date()->format('m/d/y H:i:s');
        $customerid = $this->customerSession->getCustomer()->getId();

        $checkcustomer = $this->detailfactory->create()->getCollection()
                               ->addFieldToFilter('vendor_id', $params['id'])
                               ->addFieldToFilter('customer_id', $customerid);
        if (count($checkcustomer)==0) {
            $detailfactory = $this->detailfactory->create();
            $detailfactory->setVendorId($params['id']);
            $detailfactory->setCustomerId($customerid);
            $detailfactory->save();
            $check = $this->detailfactory->create()->getCollection()
                                ->addFieldToFilter('customer_id', $customerid)
                                ->addFieldToFilter('vendor_id', $params['id'])
                                ->getFirstItem();
            $msgid = $check->getId();
            $messagefactory = $this->messagefactory->create();
            $messagefactory->setType('customer');
            $messagefactory->setMsgId($msgid);
            $messagefactory->setContent($params['message']);
            $messagefactory->setCreatedAt($created_at);
            $messagefactory->save();
         // $this->_redirect('marketplace/vendor/profile/id/'.$params['vendor_id']);
            $this->messageManager->addSuccess(__('Your Message has been sent successfully'));
        } else {
            $msgid = $checkcustomer->getFirstItem()->getId();
            $messagefactory = $this->messagefactory->create();
            $messagefactory->setMsgId($msgid);
            $messagefactory->setType('customer');
            $messagefactory->setContent($params['message']);
            $messagefactory->setCreatedAt($created_at);
            $messagefactory->save();
            //$this->_redirect('marketplace/vendor/profile/id/'.$params['vendor_id']);
            $this->messageManager->addSuccess(__('Your Message has been sent successfully'));
        }
    }
}
