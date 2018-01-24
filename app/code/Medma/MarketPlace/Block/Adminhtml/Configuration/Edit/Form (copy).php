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
 
namespace Medma\MarketPlace\Block\Adminhtml\Configuration\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
   
    protected $configurationFactory;
    protected $_formFactory;
    protected $coreregistry;
    protected $systemStore;
    protected $adminSession;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreregistry,
        \Magento\Framework\Data\FormFactory $_formFactory,
        \Medma\MarketPlace\Model\Configuration\DataFactory $configurationFactory,
        \Medma\MarketPlace\Model\ConfigurationFactory $configFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Backend\Model\Auth\Session $adminSession,
        array $data = []
    ) {
        $this->configurationFactory = $configurationFactory;
        $this->systemStore = $systemStore;
        $this->configFactory = $configFactory;
        $this->_formFactory = $_formFactory;
        $this->coreregistry = $coreregistry;
        $this->adminSession = $adminSession;
        parent::__construct($context, $coreregistry, $_formFactory, $data);
    }
 

    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */

        $form = $this->_formFactory->create(
            [
              'data' => [
                  'id' => 'edit_form',
                  'action' => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id')]),
                  'method' => 'post',
                  'enctype' => 'multipart/form-data'
              ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        $groupCollection = $this->configurationFactory->create()->getCollection();
        $groupCollection->getSelect()->group('group');

        
        foreach ($groupCollection as $group) {
            $groupName = $group['group'];
            $groupId = str_replace(' ', '', strtolower($groupName));
            
            $fieldset = $form->addFieldset($groupId, ['legend' => $groupName]);
            
            $fieldCollection = $this->configurationFactory->create()->getCollection();

            //echo "<pre>"; print_r($fieldCollection->getData());die;
            
            foreach ($fieldCollection as $field) {
                if ($field->getGroup() == $groupName) {
                    $data = $this->_createField($field);
                    $fieldset->addField($field->getCode(), $field->getType(), $data);
                    unset($data);
                }
            }
        }

        
        
        return parent::_prepareForm();
    }
    protected function _createField($field)
    {
        $vendorId = $this->getRequest()->getParam('id');
        if (!isset($vendorId)) {
            $vendorId = $this->adminSession->getUser()->getId();
        }
        
        $configValue = $this->configFactory->create()->getCollection()->addFieldToSelect('value')->addFieldToFilter('vendor_id', $vendorId)->getData();
        if (count($configValue)) {
            $jsonValue =  $configValue['0']['value'];
            $objectArray = json_decode($jsonValue);
            $arrayValue=(array)($objectArray);
            $fieldName = $field->getName();
            if (isset($arrayValue[$fieldName]) && $arrayValue[$fieldName]=='1') {
                $fieldValue = 1;
            } else {
                $fieldValue = 0;
            }
        } else {
            $fieldValue = 0;
        }
        $type = $field->getType();
                    
        $data = [
            'name'      => $field->getName(),
            'label'     => __($field->getLabel()),
            'title'     => __($field->getTitle()),
            'class'         => $field->getClass(),
            'required'  => $field->getRequired(),
            'style'         => $field->getStyle(),
            'value'     => $fieldValue,
            'after_element_html' => $field->getAfterElementHtml(),
        ];
        
        if ($field->getDisable()) {
            $data['disable'] = $field->getDisable();
        }
        if ($field->getReadonly()) {
            $data['readonly'] = $field->getReadonly();
        }
            
        if ($type == 'select') {
            $source_model = $field->getSourceModel();
        
            $options = $field->getOptions();
            if (isset($source_model) && !is_null($source_model)) {
                $data['options'] = [0 => __('No'), 1 => __('Yes')];
            } elseif (isset($options) && !is_null($options)) {
                $data['options'] = [0 => __('No'), 1 => __('Yes')];
            }
        }
        
        if ($type == 'date') {
        //$data['image'] = $this->getSkinUrl('images/grid-cal.gif');
            //$data['format'] = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        }
        
        return $data;
    }
}
