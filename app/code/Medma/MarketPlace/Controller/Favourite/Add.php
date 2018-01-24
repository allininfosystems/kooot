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

namespace Medma\MarketPlace\Controller\Favourite;

class Add extends \Magento\Framework\App\Action\Action
{
  
   
  /**
   * @var \Magento\Framework\Controller\Result\ForwardFactory
   */
    protected $_coreRegistry = null;
    
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
     
    protected $request;
    protected $marketHelper;
    protected $profile;
    protected $messageManager;
     
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Medma\MarketPlace\Helper\Data $marketHelper,
        \Medma\MarketPlace\Model\ProfileFactory $profile
    ) {
        $this->resultPageFactory  = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->request =  $context->getRequest();
        $this->profile = $profile;
        $this->marketHelper = $marketHelper;
        $this->messageManager = $context->getMessageManager();
        
        parent::__construct($context);
    }
  
    /**
     * Favourite Listing page
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $customerId = $this->marketHelper->getLoggedInUser();
        if (!$customerId) {
            $this->_redirect($this->_redirect->getRefererUrl());
            return;
        }
            
        $vendorId = $this->getRequest()->getParam('id');
        
        $profilemodel = $this->profile->create();
        $profileModel = $profilemodel->load($vendorId);
        $favourites = $profileModel->getFavourites();
                
        if (is_null($favourites) || empty($favourites)) {
            $favourites = [$customerId];
        } else {
            $favourites = json_decode($favourites, true);
            if (!in_array($customerId, $favourites)) {
                $favourites[] = $customerId;
                $this->messageManager->addSuccess(__("Seller added to your favorite list."));
            } else {
                $this->messageManager->addNotice(__("Seller already added to your favorite list."));
            }
        }
        
        $profileModel->setFavourites(json_encode($favourites))->save();
        $this->_redirect($this->_redirect->getRefererUrl());
    }
}
