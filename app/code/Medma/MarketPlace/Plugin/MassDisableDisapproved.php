<?php

namespace Medma\MarketPlace\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Catalog\Api\ProductRepositoryInterface;


class MassDisableDisapproved
{
    protected $resultFactory;

    protected $messageManager;

    protected $attributeHelper;

    protected $productRepository;

    public function __construct(
        RequestInterface $request ,
        MessageManager $messageManager,
        \Magento\Catalog\Helper\Product\Edit\Action\Attribute $attributeHelper,
        ProductRepositoryInterface $productRepository
        )
    {
        $this->attributeHelper = $attributeHelper;
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->productRepository = $productRepository;

    }  

    public function aroundExecute(\Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save $subject, \Closure $proceed)
    {

        $idArray = $this->attributeHelper->getProductIds();
        for($i=0;$i<count($idArray);$i++) {
             $product = $this->productRepository->getById($idArray[$i]);
              $prod_name = $product->getName();
              $prod_namewithoutspace = str_replace(' ', '', $prod_name);
              $prod_namewithup = strtoupper($prod_namewithoutspace);
              $product->setNameKeyword($prod_namewithup);
              $this->productRepository->save($product);
        }
        $paramArray  = $this->request->getParam('attributes', []);
        if (array_key_exists("approved",$paramArray))
        {
        	if($paramArray['approved'] == 0)
        	{	
		        for($i=0;$i<count($idArray);$i++) {
		             $product = $this->productRepository->getById($idArray[$i]);
		             $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
		             $this->productRepository->save($product);
		        }
		         $this->messageManager->addWarning(__("The unapproved products were disabled"));
	     	}

	     	if($paramArray['approved'] == 2)
        	{	
		        for($i=0;$i<count($idArray);$i++) {
		             $product = $this->productRepository->getById($idArray[$i]);
		             $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
		             $this->productRepository->save($product);
		        }
		         $this->messageManager->addWarning(__("The rejected products were disabled"));
	     	}

        }

        return $proceed(); 

    }
}
