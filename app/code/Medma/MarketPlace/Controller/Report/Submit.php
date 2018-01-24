<?php

namespace Medma\MarketPlace\Controller\Report;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Submit extends \Magento\Framework\App\Action\Action
{
    
    protected $scopeConfig;
    protected $catalogSession;
    protected $transportBuilder;
    protected $inlineTranslation;

    public function __construct(
        Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
    ) {
        $this->catalogSession = $catalogSession;
        $this->scopeConfig = $scopeConfig;
        $this->_transportBuilder = $transportBuilder;
           $this->_inlineTranslation = $inlineTranslation;
        parent::__construct($context);
    }
    

    public function execute()
    {
        $post = $this->getRequest()->getPost();
        $emailTemplateVariables = [];
        $uploaded_files = $this->catalogSession->getUploadedImages();
    
        $emailTempVariables['reporting_type'] =  $post['reporting_type'];
        $emailTempVariables['full_name'] =  $post['full_name'];
        $emailTempVariables['company'] =  $post['company'];
        $emailTempVariables['phone'] =  $post['phone'];
        $emailTempVariables['message'] =  $post['message'];
        $emailTempVariables['email'] =   $post['email'];
        $senderName = $post['full_name'];
        $senderEmail = $post['email'];

        $email = $this->scopeConfig->getValue('report/general/to');

        $copyemail = $this->scopeConfig->getValue('report/general/copy_to');
    
        $bccemail = $this->scopeConfig->getValue('report/general/bcc_to');
    
        $postObject = new \Magento\Framework\DataObject();
        $postObject->setData($emailTempVariables);
        $this->_inlineTranslation->suspend();

        $sender = [
                 'name' => $senderName,
                 'email' => $senderEmail,
                  ];
        if ($post['send_copy']=="on") {
            $copyemail = $copyemail.", ".$senderEmail;
        }
    
                            $this->_transportBuilder->setTemplateIdentifier('report_email_template')
                                ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
                                ->setTemplateVars($emailTempVariables)
                                ->setFrom($sender)
                                ->addTo($email)
                                ->addCc($copyemail)
                                ->addBcc($bccemail)
                                ->addAttachment($uploaded_files);
                
        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

    
        $this->catalogSession->setUploadedImages("");
        $this->messageManager->addSuccess(__('Your Report has been submitted successfully.'));
        $this->_redirect($this->_redirect->getRefererUrl());
    }
}
