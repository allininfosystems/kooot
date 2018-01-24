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
  
namespace Medma\MarketPlace\Block;
 
class Vendor extends \Magento\Framework\View\Element\Template
{
   /**
    * @var \Medma\MarketPlace\Model\ProoftypeFactory $prooftype
    */
    protected $prooftype;

   /**
    * @var \Medma\MarketPlace\Helper\Data $marketHelper
    */
    protected $marketHelper;
   

    
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Medma\MarketPlace\Model\ProoftypeFactory $prooftype,
        \Medma\MarketPlace\Helper\Data $marketHelper
    ) {
    
        $this->prooftype = $prooftype;
        $this->marketHelper = $marketHelper;
        parent::__construct($context);
    }
    
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getProofTypes()
    {
        $proofCollection = $this->prooftype->create()->getCollection()->addFieldToFilter("status", "1");
        return $proofCollection;
    }
    
    public function getPostActionUrl()
    {
        return $this->getUrl("marketplace/vendor/saveprofile/");
    }
}
