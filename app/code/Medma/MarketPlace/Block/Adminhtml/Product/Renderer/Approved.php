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


namespace Medma\MarketPlace\Block\Adminhtml\Product\Renderer;

class Approved extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
       
        parent::__construct($context);
        $this->_productFactory = $productFactory;
    }
   
    /**
     * @method : this method render categories assigned to the particular product
     * @param  : row id of the grid
     * @return : array
     */
    public function render(\Magento\Framework\DataObject $row)
    {

      //$productCategories = array();
        $row->getData('entity_id');
        $product = $this->_productFactory->create()->load($row->getData('entity_id'));
        $approved = $product->getApproved();
        if ($approved==0) {
            $approved_title="No";
        } else {
            $approved_title="Yes";
        }
        return $approved_title;
    }
}
