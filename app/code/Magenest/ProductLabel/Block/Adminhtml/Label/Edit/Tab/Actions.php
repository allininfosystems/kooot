<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ProductLabel
 * @author   ThaoPV <thaopw@gmail.com>
 */
namespace Magenest\ProductLabel\Block\Adminhtml\Label\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic as FormGeneric;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Store\Model\System\Store as SystemStore;
use Magenest\ProductLabel\Model\Status;

/**
 * Class Actions
 *
 * @package Magenest\ProductLabel\Block\Adminhtml\Label\Edit\Tab
 */
class Actions extends FormGeneric implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magenest\ProductLabel\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magento\Store\Model\System\Store       $systemStore
     * @param \Magenest\ProductLabel\Model\Status     $status
     * @param array                                   $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        SystemStore $systemStore,
        Status $status,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return                                        $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Magenest\ProductLabel\Model\Label */
        $model = $this->_coreRegistry->registry('product_label');

        /**
 * @var \Magento\Framework\Data\Form $form
*/
        $form = $this->_formFactory->create(
            ['enctype' => 'multipart/form-data']
        );
        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('category_fieldset', ['legend' => __(' Category Page Label')]);

        $fieldset->addField(
            'category_display',
            'select',
            [
                'name' => 'category_display',
                'label' => __('Display'),
                'title' => __('Display'),
                'required' => true,
                'options' => $this->_status->getOptionArray(),
            ]
        );
        $fieldset->addField(
            'category_position',
            'select',
            [
                'name' => 'category_position',
                'label' => __('Position'),
                'title' => __('Position'),
                'required' =>true,
                'options' => $this->_status->getOptionPosition(),
            ]
        );
        $fieldset->addField(
            'category_image',
            'image',
            [
                'name' => 'category_image',
                'label' => __('Image'),
                'title' => __('Image'),
                'note' => 'Allow image type: jpg, jpeg, gif, png',
            ]
        );
        $fieldset->addField(
            'category_text',
            'text',
            [
                'name' => 'category_text',
                'label' => __('Text'),
                'title' => __('Text'),
                'note' => __('You can use predefined values in this field. Please refer to extension manual.')
            ]
        );

        $fieldset2 = $form->addFieldset('product_fieldset', ['legend' => __('Product Page Label')]);

        $fieldset2->addField(
            'product_display',
            'select',
            [
                'name' => 'product_display',
                'label' => __('Display'),
                'title' => __('Display'),
                'required' => true,
                'options' => $this->_status->getOptionArray(),
            ]
        );
        $fieldset2->addField(
            'product_position',
            'select',
            [
                'name' => 'product_position',
                'label' => __('Position'),
                'title' => __('Position'),
                'required' =>true,
                'options' => $this->_status->getOptionPosition(),
            ]
        );
        $fieldset2->addField(
            'product_image',
            'image',
            [
                'name' => 'product_image',
                'label' => __('Image'),
                'title' => __('Image'),
                'note' => 'Allow image type: jpg, jpeg, gif, png',
            ]
        );
        $fieldset2->addField(
            'product_text',
            'text',
            [
                'name' => 'product_text',
                'label' => __('Text'),
                'title' => __('Text'),
                'note' => __('You can use predefined values in this field. Please refer to extension manual.')
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Actions');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Actions');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
