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
 
namespace Medma\MarketPlace\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;

class SellerLayoutObserver implements ObserverInterface
{

    protected $observer;
    protected $marketHelper;
    protected $request;
    protected $_resourceConfig;
    
    public function __construct(
        \Medma\MarketPlace\Helper\Data $marketHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig
    ) {
        $this->marketHelper = $marketHelper;
        $this->request = $request;
        $this->_resourceConfig = $resourceConfig;
    }
 

    public function execute(Observer $observer)
    {
        
        $request_data = $this->request->getPost('groups');
        
        $layout_data = $request_data['general']['fields']['shop_info_display']['value'];
        
        if ($layout_data!="product_info") {
            $product_info_layout = 0;
            $right_left_layout = 1;
        } else {
            $product_info_layout = 1;
            $right_left_layout = 0;
        }
        
        $scope = 'default';
        $scopeId = 0;
        
        $path = 'marketplace/general/product_info_layout';
        $value = $product_info_layout;
        $this->_resourceConfig->saveConfig($path, $value, $scope, $scopeId);
        
        $path = 'marketplace/general/right_left_layout';
        $value = $right_left_layout;
        $this->_resourceConfig->saveConfig($path, $value, $scope, $scopeId);
    }
}
