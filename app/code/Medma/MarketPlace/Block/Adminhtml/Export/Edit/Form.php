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
 
namespace Medma\MarketPlace\Block\Adminhtml\Export\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\ImportExport\Model\Source\Export\EntityFactory
     */
    protected $_entityFactory;

    /**
     * @var \Magento\ImportExport\Model\Source\Export\FormatFactory
     */
    protected $_formatFactory;
    protected $vendorHelper;
    protected $adminSession;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\ImportExport\Model\Source\Export\EntityFactory $entityFactory
     * @param \Magento\ImportExport\Model\Source\Export\FormatFactory $formatFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\ImportExport\Model\Source\Export\EntityFactory $entityFactory,
        \Magento\ImportExport\Model\Source\Export\FormatFactory $formatFactory,
        \Medma\MarketPlace\Helper\Data $vendorHelper,
        \Magento\Backend\Model\Auth\Session $adminSession,
        array $data = []
    ) {
        $this->adminSession = $adminSession;
        $this->vendorHelper = $vendorHelper;
        $this->_entityFactory = $entityFactory;
        $this->_formatFactory = $formatFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form before rendering HTML.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        
        $roleId = $this->vendorHelper->getConfig('general', 'vendor_role');
        $current_user = $this->adminSession->getUser();
        if ($current_user->getRole()->getRoleId() == $roleId) {
            $original_options = $this->_entityFactory->create()->toOptionArray();
            $options = [];
            $options[] = ['label' => __('-- Please Select --'), 'value' => ''];
            foreach ($original_options as $option) {
                if ($option['value']=='catalog_product') {
                    $options[] = ['label' => __($option['label']), 'value' => $option['value']];
                }
            }
        } else {
            $options = $this->_entityFactory->create()->toOptionArray();
        }
        

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('adminhtml/*/getFilter'),
                    'method' => 'post',
                ],
            ]
        );

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Export Settings')]);
        $fieldset->addField(
            'entity',
            'select',
            [
                'name' => 'entity',
                'title' => __('Entity Type'),
                'label' => __('Entity Type'),
                'required' => false,
                'onchange' => 'varienExport.getFilter();',
                'values' => $options
            ]
        );
        $fieldset->addField(
            'file_format',
            'select',
            [
                'name' => 'file_format',
                'title' => __('Export File Format'),
                'label' => __('Export File Format'),
                'required' => false,
                'values' => $this->_formatFactory->create()->toOptionArray()
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
