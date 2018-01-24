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

namespace Medma\MarketPlace\Block\Adminhtml\Vendor\Renderer;

class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
       
        parent::__construct($context);
        $this->customerFactory = $customerFactory;
    }
   
    /**
     * @method : this method render categories assigned to the particular product
     * @param  : row id of the grid
     * @return : array
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($row->getData($this->getColumn()->getIndex())==1) {
            return "<div style='color:green'>&#10004;</div>";
        } else {
            return "<div style='color:red'>&#x2718;</div>";
        }
    }
}
