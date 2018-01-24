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
 
namespace Medma\MarketPlace\Block\Adminhtml\Transaction;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Medma_MarketPlace';
        $this->_controller = 'adminhtml_vendor';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Transaction'));
        $this->buttonList->remove('reset');
        $this->buttonList->remove('back');
       
         $this->addButton(
             'my_back',
             [
                'label' => __('Back'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/index', ['id' => $this->getRequest()->getParam('vendor_id')]) . '\')',
                'class' => 'back'
             ],
             -1
         );
    }
}
