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

class Vendor extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    protected $userFactory;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\User\Model\UserFactory $userFactory
    ) {
       
        parent::__construct($context);
        $this->_productFactory = $productFactory;
        $this->userFactory = $userFactory;
    }
   
    /**
     * @method : this method render categories assigned to the particular product
     * @param  : row id of the grid
     * @return : array
     */
    public function render(\Magento\Framework\DataObject $row)
    {

        $product = $this->_productFactory->create()->load($row->getData('entity_id'));
        $userObject = $this->userFactory->create()->load($product->getVendor());
        return $userObject->getName();
    }
}
