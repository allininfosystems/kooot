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
 
namespace Medma\MarketPlace\Block\Adminhtml;

class Vendor extends \Magento\Backend\Block\Widget\Grid\Container
{
 /**
  * Constructor
  *
  * @return void
  */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_vendor';
        $this->_blockGroup = 'Medma_MarketPlace';
        $this->_headerText = __('Add Vendor');
        $this->_addButtonLabel = __('Add Vendor');
        parent::_construct();
    }
}
