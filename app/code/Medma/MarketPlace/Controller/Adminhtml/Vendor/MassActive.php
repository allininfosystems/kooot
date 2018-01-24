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
 
class MassActive extends \Magento\Backend\App\Action
{
    protected $filter;
    protected $infoFactory;
    protected $userFactory;
    protected $productRepository;
    protected $productCollection;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Medma\MarketPlace\Model\ProfileFactory $infoFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection,
        \Medma\MarketPlace\Helper\Data $marketHelper
    ) {
        $this->productCollection  = $productCollection;
        $this->productRepository = $productRepository;
        $this->infoFactory = $infoFactory;
        $this->userFactory  = $userFactory;
        $this->marketHelper = $marketHelper;
        parent::__construct($context);
    }
 
    public function execute()
    {
          $post = $this->getRequest()->getPost();
          $activationEmailEnable = $this->marketHelper->getConfig('vendor_activation_email', 'active_vendor_email');
          foreach ($post["massaction"] as $info) {
            $data = $this->userFactory->create()->load($info)->getIsActive();
            $products = $this->productCollection->load()->addFieldToFilter('vendor', $info);
            foreach ($products as $product) {
                $current_product =$this->productRepository->get($product->getSku());
                if ($current_product->getVendor()==$info) {
                    $current_product = $this->productRepository->get($product->getSku());
                    $current_product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                    $this->productRepository->save($current_product);
                }
            }
            $dr =$this->userFactory->create()->getCollection()->addFieldToFilter('user_id', $info);
            $vendorInfo = $this->userFactory->create()->load($info);
            $vendorInfo->setIsActive('1')->save();
            $firstName = $vendorInfo->getFirstname();
            $emailId = $vendorInfo->getEmail();
            /* sending activation email */
            if($activationEmailEnable == 1)
            {
                $vendorSender = $this->marketHelper->getTemplateId('marketplace/vendor_activation_email/email_sender');
                $vendorsendername = $this->marketHelper->getTemplateId('trans_email/ident_'.$vendorSender.'/name');
                $vendorsenderemail = $this->marketHelper->getTemplateId('trans_email/ident_'.$vendorSender.'/email');
                $vendorReceiverInfo = [
                'name' => $firstName,
                'email' => $emailId
                ];
                $vendorSenderInfo = [
                'name' => $vendorsendername,
                'email' => $vendorsenderemail,
                ];
                                                
                $vendorEmailTemplateVariables = [];
                $vendorEmailTemplateVariables['myvar1'] = $firstName;
                $this->marketHelper->sendActivationEmailToVendor(
                                    $vendorEmailTemplateVariables,
                                    $vendorSenderInfo,
                                    $vendorReceiverInfo
                                    );
            }
        }
          $this->messageManager->addSuccess(__(" Vendor(s) activated successfully"));
          $this->_redirect($this->_redirect->getRefererUrl());
          return;
    }
}
