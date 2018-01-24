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

class MarketplaceObserver implements ObserverInterface
{

    protected $observer;
    protected $marketHelper;
    protected $request;
    protected $layout;
    
    public function __construct(
        \Medma\MarketPlace\Helper\Data $marketHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $this->marketHelper = $marketHelper;
        $this->request = $request;
        $this->layout = $layout;
    }
 

    public function execute(Observer $observer)
    {
        
        $fullActionName = $this->request->getFullActionName();
        
        $position = $this->marketHelper->getConfig("general", "shop_info_display");
        
        if ($position != 'product_info') {
            $myXml = '<referenceContainer name="sidebar.additional" >';
            $myXml .= '	<block class="Medma\MarketPlace\Block\Catalog\Product\Vendor\Info" name="marketplace.catalog_product_vendor_info" 
						template="Medma_MarketPlace::catalog/product/vendor/info.phtml">';
            $myXml .= '	<arguments><argument name="title" xsi:type="string">sellerinformation</argument></arguments></block>';
            $myXml .= '</referenceContainer>';
        } else {
            $myXml = '<referenceBlock name="product.info.details">';
            $myXml .= '	<block class="Medma\MarketPlace\Block\Catalog\Product\Vendor\Info" name="marketplace.catalog_product_vendor_info" template="Medma_MarketPlace::catalog/product/vendor/info.phtml" group="detailed_info" >';
            $myXml .= '	<arguments><argument translate="true" name="title" xsi:type="string">Seller Information</argument></arguments> </block>';
            $myXml .= '	</referenceBlock>';
        }
        
        //$layout = $observer->getEvent()->getLayout();
        $layout = $this->layout;
        if ($fullActionName=='catalog_product_view') {
            $layout->getUpdate()->addUpdate($myXml);
            //$layout->generateXml();
        }
    }
}
