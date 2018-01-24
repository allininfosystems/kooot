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
 
class MassInactive extends \Magento\Backend\App\Action
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
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
    ) {
        $this->productCollection  = $productCollection;
        $this->productRepository = $productRepository;
        $this->infoFactory = $infoFactory;
        $this->userFactory  = $userFactory;
        parent::__construct($context);
    }
 
    public function execute()
    {
          $post = $this->getRequest()->getPost();
        foreach ($post["massaction"] as $info) {
            $data = $this->userFactory->create()->load($info)->getIsActive();
            $products = $this->productCollection->load()->addFieldToFilter('vendor', $info);
            foreach ($products as $product) {
                $current_product =$this->productRepository->get($product->getSku());
                if ($current_product->getVendor()==$info) {
                    $current_product = $this->productRepository->get($product->getSku());
                    $current_product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                    $this->productRepository->save($current_product);
                }
            }
            $dr =$this->userFactory->create()->getCollection()->addFieldToFilter('user_id', $info);
            $this->userFactory->create()->load($info)->setIsActive('0')->save();
        }
          $this->messageManager->addSuccess(__(" Vendor(s) inactivated successfully"));
          $this->_redirect($this->_redirect->getRefererUrl());
          return;
    }
}
