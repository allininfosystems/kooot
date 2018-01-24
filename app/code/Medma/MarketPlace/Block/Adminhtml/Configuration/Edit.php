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
 
namespace Medma\MarketPlace\Block\Adminhtml\Configuration;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Medma\MarketPlace\Helper\Data $vendorHelper,
        \Magento\Backend\Model\Auth\Session $adminSession
    ) {

        $this->adminSession = $adminSession;
        $this->vendorHelper = $vendorHelper;

        parent::__construct($context);
    }
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Medma_MarketPlace';
        $this->_controller = 'adminhtml_configuration';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Configuration'));
        $this->buttonList->remove('reset');
        $this->buttonList->remove('delete');
        $this->buttonList->remove('back');
       
        $roleId = $this->vendorHelper->getConfig('general', 'vendor_role');
         $current_user = $this->adminSession->getUser();
        if ($current_user->getRole()->getRoleId() != $roleId) {
            $this->addButton(
                'my_back',
                [
                'label' => __('Back'),
                'onclick' => 'setLocation(\'' . $this->getUrl('admin_marketplace/vendor/index') . '\')',
                'class' => 'back'
                ],
                -1
            );
        }
    }
}
