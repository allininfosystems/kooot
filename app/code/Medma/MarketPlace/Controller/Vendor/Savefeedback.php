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

class Savefeedback extends \Magento\Framework\App\Action\Action
{

    protected $feedfactory;
    protected $messageManager;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Medma\MarketPlace\Model\FeedbackFactory $feedfactory
    ) {
        $this->feedfactory  = $feedfactory;
        $this->messageManager = $context->getMessageManager();
         parent::__construct($context);
    }
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $created_at = date("Y-m-d H:i:s");
        $feedfactory = $this->feedfactory->create();
        $feedfactory = $feedfactory->setVendorId($data['seller_id'])
                                   ->setCustomerId($data['customer_id'])
                                   ->setQuality($data['feed_description'])
                                   ->setPrice($data['feed_price'])
                                   ->setShipping($data['feed_shipping'])
                                   ->setNickname($data['feed_nickname'])
                                   ->setSummary($data['feed_summary'])
                                   ->setReview($data['feed_review'])
                                   ->setCreatedAt($created_at);
                                   
        try {
            $feedfactory->save();
            $this->_redirect($this->_redirect->getRefererUrl());
            $this->messageManager->addSuccess(__('Your Feedback submitted successfully'));
        } catch (\Magento\Framework\Validator\Exception $e) {
            $this->_redirect($this->_redirect->getRefererUrl());
            $this->messageManager->addError(__('Your Feedback is not submitted'));
        }
    }
}
