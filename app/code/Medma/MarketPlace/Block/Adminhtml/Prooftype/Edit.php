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
  
namespace Medma\MarketPlace\Block\Adminhtml\Prooftype;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected $_coreRegistry;
 
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
 
    /**
     * Department edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'Medma_MarketPlace';
        $this->_controller = 'adminhtml_prooftype';
        parent::_construct();
 
        $this->buttonList->update('save', 'label', __('Save'));
        
         $this->addButton(
             'delete',
             [
                'label' => __('Delete'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/delete', ['id' => $this->getRequest()->getParam('id')]) . '\')',
                'class' => 'delete'
             ],
             -1
         );
      
        //$this->buttonList->update('delete', 'label', __('Delete'));
    }
 
    /**
     * Get header with Department name
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('medma_marketplace_prooftype')->getId()) {
            return __("Edit Type '%1'", $this->escapeHtml($this->_coreRegistry->registry('medma_marketplace_prooftype')->getName()));
        } else {
            return __('New Type');
        }
    }
 
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
 
    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('admin_marketplace/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('post_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'post_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'post_content');
                }
            };
        ";
 
        return parent::_prepareLayout();
    }
}
