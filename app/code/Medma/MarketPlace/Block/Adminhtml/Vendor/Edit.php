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
 
namespace Medma\MarketPlace\Block\Adminhtml\Vendor;

/**
 * CMS block edit form container
 */
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
        $this->_controller = 'adminhtml_vendor';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->update('delete', 'label', __('Delete'));

        $this->buttonList->remove('reset');
        
        $roleId = $this->vendorHelper->getConfig('general', 'vendor_role');
         $current_user = $this->adminSession->getUser();
        if ($current_user->getRole()->getRoleId() == $roleId) {
            $this->buttonList->remove('back');
            $this->addButton(
                'delete',
                [
                'label' => __('Delete'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/delete', ['id' => $this->getRequest()->getParam('vendor_id')]) . '\')',
                'class' => 'delete'
                ],
                -1
            );
        }
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('medma_marketplace_profile')->getId()) {
            return __("Edit Item '%1'", $this->escapeHtml($this->_coreRegistry->registry('medma_marketplace_profile')->getTitle()));
        } else {
            return __('New Item');
        }
    }
}
