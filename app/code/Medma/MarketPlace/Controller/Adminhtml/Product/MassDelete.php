<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Medma\MarketPlace\Controller\Adminhtml\Product;

use Magento\Framework\Controller\ResultFactory;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class MassDelete extends \Magento\Catalog\Controller\Adminhtml\Product\MassDelete
{
    
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Builder $productBuilder
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Builder $productBuilder,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Medma\MarketPlace\Helper\Data $marketplacehelper,
        \Magento\User\Model\User $modelUser,
        \Magento\Catalog\Model\Product $productLoader
    ) {
        
        $this->marketplaceHelper = $marketplacehelper;
        $this->modelUser = $modelUser;
        $this->proLoader = $productLoader; 
        parent::__construct($context, $productBuilder, $filter, $collectionFactory);
    }
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $adminUserId = $this->marketplaceHelper->getLoggedInAdminUserId();
        $userInfo = $this->modelUser->load($adminUserId);
        $roleData = $userInfo->getRole()->getData();
        $productLoader = $this->proLoader;
        $productDeleted = 0;
        if($roleData['role_name']=="VENDORS")
        {
            foreach ($collection->getItems() as $product) {
                
                if($productLoader->load($product->getId())->getVendor()==$adminUserId)
                {
                    $product->delete();
                    $productDeleted++;
                }
            }
        }
        else
        {
            foreach ($collection->getItems() as $product) {
                $product->delete();
                $productDeleted++;
            }
        }
        
        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $productDeleted)
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('catalog/*/index');
    }
}
