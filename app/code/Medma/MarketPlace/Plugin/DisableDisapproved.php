<?php

namespace Medma\MarketPlace\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface as MessageManager;

class DisableDisapproved
{
    protected $resultFactory;

    protected $messageManager;

    public function __construct(RequestInterface $request ,MessageManager $messageManager)
    {
        $this->request = $request;
        $this->messageManager = $messageManager;
    }  

    public function aroundExecute(\Magento\Catalog\Controller\Adminhtml\Product\Save $subject, \Closure $proceed)
    {
        $post = $this->request->getPost();
        $pro = $post['product'];
        
        if($pro['approved'] == 0 )
        {
            if($pro['status'] == 1)
            {
                $pro['status'] = 0;
                $this->messageManager->addWarning(__("The product has been disabled as it is not approved by store admin"));     
            }    
            
        }

        if( $pro['approved'] == 2 )
        {
            if($pro['status'] == 1)
            {
                $pro['status'] = 0;
                $this->messageManager->addWarning(__("The product has been disabled as it has been rejected by store admin"));     
            }    
            
        }

        $this->request->setPostValue('product',$pro);
	    return $proceed();
    }
}
