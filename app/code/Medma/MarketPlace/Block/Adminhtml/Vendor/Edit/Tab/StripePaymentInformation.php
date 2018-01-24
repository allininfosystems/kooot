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
 
namespace Medma\MarketPlace\Block\Adminhtml\Vendor\Edit\Tab;

class StripePaymentInformation extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    protected $countryCollectionFactory;
    protected $ProfileFactory;
    protected $vendorHelper;
    protected $prooftypeFactory;
    protected $adminSession;
    protected $pricehelper;
    protected $coreregistry;
    protected $session;

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
        \Magento\Directory\Model\Config\Source\Country $countryCollectionFactory,
        \Medma\MarketPlace\Model\ProfileFactory $ProfileFactory,
        \Medma\MarketPlace\Model\ProoftypeFactory $prooftypeFactory,
        \Medma\MarketPlace\Helper\Data $vendorHelper,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Framework\Pricing\Helper\Data $pricehelper,
        \Magento\Framework\Session\SessionManager $session,
        array $data = []
    ) {
        $this->ProfileFactory = $ProfileFactory;
        $this->session = $session;
        $this->prooftypeFactory = $prooftypeFactory;
        $this->_systemStore = $systemStore;
        $this->adminSession = $adminSession;
        $this->vendorHelper = $vendorHelper;
        $this->pricehelper = $pricehelper;
        $this->coreregistry = $coreregistry;
        
        $this->_countryCollectionFactory = $countryCollectionFactory;
        parent::__construct($context, $coreregistry, $formFactory, $data);
    }
    

    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        if ($this->session->getUserData()) {
            $model=$this->session->getUserData();
                
        }
        
        
        $roleId = $this->vendorHelper->getConfig('general', 'vendor_role');
         $current_user = $this->adminSession->getUser();
        if ($current_user->getRole()->getRoleId() == $roleId) {
            $userId=$current_user->getId();
        } else {
            $userId = $this->getRequest()->getParam('id');
        }
      
        $countries=[];
        $countries=$this->_countryCollectionFactory->toOptionArray();

        if ($userId) {
            $model = $this->ProfileFactory->create()->getCollection()->addFieldToFilter('user_id', $userId)->getFirstItem();

        } else {
            $model['stripe_email_id']='';
            $model['stripe_secret_api_key']='';
            $model['stripe_publishable_api_key']='';
        }

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Stripe Payment Information')]);

        
        $fieldset->addField(
            'stripe_email_id',
            'text',
            [
                                    'name' => 'stripe_email_id',
                                    'label' => __('Stripe Email ID'),
                                    'title' => __('Stripe Email ID'),
                                    'value' => $model['stripe_email_id'],
                                    'required' => false,
                            ]
        );
        $fieldset->addField(
            'stripe_secret_api_key',
            'text',
            [
                                    'name' => 'stripe_secret_api_key',
                                    'label' => __('Stripe Secret API Key'),
                                    'title' => __('Stripe Secret API Key'),
                                    'value' => $model['stripe_secret_api_key'],
                                    'required' => false,
                            ]
        );
        $fieldset->addField(
            'stripe_publishable_api_key',
            'text',
            [
                                    'name' => 'stripe_publishable_api_key',
                                    'label' => __('Stripe Publishable API Key'),
                                    'title' => __('Stripe Publishable API Key'),
                                    'value' => $model['stripe_publishable_api_key'],
                                    'required' => false,
                            ]
        );
        $roleId = $this->vendorHelper->getConfig('general', 'vendor_role');
        //$current_user = $this->adminSession->getUser();
        if ($userId) {
            $form->setValues($model->getData());
        }
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
        return __('Stripe Payment Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Stripe Payment Information');
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
