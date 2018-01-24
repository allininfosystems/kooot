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
 
namespace Medma\MarketPlace\Block\Adminhtml\Transaction\Edit\Tab;

class Form extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    protected $ProfileFactory;

    protected $coreregistry;
    protected $adminSession;
    protected $pricehelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreregistry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Medma\MarketPlace\Model\ProfileFactory $ProfileFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Framework\Pricing\Helper\Data $pricehelper,
        array $data = []
    ) {
   
        $this->_systemStore = $systemStore;
        $this->coreregistry = $coreregistry;
        $this->ProfileFactory = $ProfileFactory;
        $this->pricehelper = $pricehelper;
        $this->adminSession = $adminSession;
        parent::__construct($context, $coreregistry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {

        $id = (int)$this->getRequest()->getParam('vendor_id');
        
        $profile = $this->ProfileFactory->create()->getCollection()->addFieldToFilter('user_id', $id)->getFirstItem();

        $remaining_amount = ($profile->getTotalVendorAmount() - $profile->getTotalVendorPaid());
        $remaining_amount = $this->pricehelper->currency($remaining_amount, true, false);
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Transaction Information')]);
    
        $fieldset->addField('vendor_id', 'hidden', ['name' => 'vendor_id','value'=>$id ]);
        $fieldset->addField('Information', 'note', [
            'text' => __('You can not transfer more then ') . '<b>' . $remaining_amount . '</b>',
        ]);

        $fieldset->addField('information', 'select', [
            'name' => 'information',
            'label' => __('Method'),
            'id' => 'information',
            'title' => __('Method'),
            'required' => true,
            'class' => 'input-select',
            'options' => ['Cash' => __('Cash'), 'Check' => __('Check')],
        ]);
        $fieldset->addField('amount', 'text', [
            'name' => 'amount',
            'label' => __('Amount'),
            'id' => 'amount',
            'title' => __('Amount'),
            'class' => 'required-entry validate-number',
            'required' => true,
            'value' => $this->adminSession->getAmount(),
        ]);

        
 
      //$form->setValues($userFactory->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Account Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Account Information');
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
        return true;
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
}
