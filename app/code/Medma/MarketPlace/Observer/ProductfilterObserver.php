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

class ProductfilterObserver implements ObserverInterface
{

    protected $observer;
    protected $request;
    protected $marketHelper;
    
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Medma\MarketPlace\Helper\Data $marketHelper
    ) {
        $this->request = $request;
        $this->marketHelper = $marketHelper;
    }

    public function execute(Observer $observer)
    {
        
        $event = $observer->getEvent();
        $collection = $event->getCollection();
        
        //echo $collection->count()."-".$collection->getSize();
    }
}
