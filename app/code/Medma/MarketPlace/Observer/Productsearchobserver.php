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

class Productsearchobserver implements ObserverInterface
{

    protected $observer;
    protected $Helper;
    protected $request;
    protected $layout;
    
    public function __construct(
        \Medma\MarketPlace\Helper\Data $Helper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $this->Helper = $Helper;
        $this->request = $request;
        $this->layout = $layout;
    }
 

    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $name = $product->getName();
   // $stringwithoutspace = str_replace(' ', '', $name);
        $string = str_replace('-', '', $name);
    
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    
        $product->setData('name_keyword', strtoupper($string))->getResource()
                 ->saveAttribute($product, 'name_keyword');
    }
}
