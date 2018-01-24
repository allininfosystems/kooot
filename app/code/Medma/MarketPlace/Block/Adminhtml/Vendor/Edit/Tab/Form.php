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

class Form extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
        const CURRENT_USER_PASSWORD_FIELD = 'current_password';
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    protected $ProfileFactory;
    protected $coresession;
    protected $userFactory;
    protected $adminSession;
    protected $vendorHelper;
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
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Medma\MarketPlace\Model\ProfileFactory $ProfileFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Medma\MarketPlace\Helper\Data $vendorHelper,
        \Magento\Framework\Registry $coreregistry,
        \Magento\Framework\Session\SessionManager $session,
        array $data = []
    ) {
   
        $this->_systemStore = $systemStore;
        $this->session = $session;
        $this->adminSession = $adminSession;
        $this->ProfileFactory = $ProfileFactory;
        $this->vendorHelper = $vendorHelper;
        $this->userFactory = $userFactory;
        $this->coreregistry = $coreregistry;    
        $this->coresession = $context->getBackendSession();
        parent::__construct($context, $coreregistry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        if ($this->session->getUserData()) {
            $userFactory=$this->session->getUserData();
                //print_r($userFactory);
        }
    
        $roleId = $this->vendorHelper->getConfig('general', 'vendor_role');
        $current_user = $this->adminSession->getUser();
        if ($current_user->getRole()->getRoleId() == $roleId) {
            $id=$current_user->getId();
        } else {
            $id = $this->getRequest()->getParam('id');
        }
    
        if ($id) {
            $userFactory = $this->userFactory->create()->load($id);
            
        } else {
             $userFactory['username']='';
             $userFactory['lastname']='';
             $userFactory['firstname']='';
             $userFactory['email']='';
             $userFactory['is_active']='';
             $userFactory['seller_vacation_mode']='';
        }
        
        
    
        
        
        $isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Account Information')]);

        if ($id) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'id',
                                'value'=>$userFactory['id']
                                ]
            );
        }
        //~ else
        //~ {
            //~ $fieldset->addField('id', 'hidden', ['name' => 'id','value' =>$id]);
        //~ }

        $fieldset->addField(
            'username',
            'text',
            [
                                    'name' => 'username',
                                    'label' => __('User Name'),
                                    'title' => __('User Name'),
                                    'value' => $userFactory['username'],
                                    'required' => true,
                                    
                            ]
        );
        $fieldset->addField(
            'firstname',
            'text',
            [
                                    'name' => 'firstname',
                                    'label' => __('First Name'),
                                    'title' => __('First Name'),
                                    'value' => $userFactory['lastname'],
                                    'required' => true,
                                ]
        );
        $fieldset->addField(
            'lastname',
            'text',
            [
                                    'name' => 'lastname',
                                    'label' => __('Last Name'),
                                    'title' => __('Last Name'),
                                    'value' => $userFactory['lastname'],
                                    'required' => true,
                            ]
        );
        $fieldset->addField(
            'email',
            'text',
            [
                                    'name' => 'email',
                                    'label' => __('Email'),
                                    'title' => __('Email'),
                                    'value' => $userFactory['email'],
                                    'required' => true,
                            ]
        );
        $fieldset->addField(
            'password',
            'password',
            [
                                    'name' => 'password',
                                    'label' => __('Password'),
                                    'title' => __('Password'),
                            ]
        );
        $fieldset->addField(
            'confirmation',
            'password',
            [
                                    'name' => 'confirmation',
                                    'label' => __('Password Confirmation'),
                                    'title' => __('Password Confirmation'),
                                ]
        );
       
        if ($current_user->getRole()->getRoleId() != $roleId) {
            
            $fieldset->addField(
                'is_active',
                'select',
                [
                                'name' => 'is_active',
                                'label' => __('This account is'),
                                'title' => __('This account is'),
                                'value' => $userFactory['is_active'],
                                'values' => ['1' => 'Active','0' => 'Inactive'],
                            ]
            );
             

        }
        if ($current_user->getRole()->getRoleId() != $roleId) {
            $fieldset->addField(
                'seller_vacation_mode',
                'select',
                [
                                'name' => 'seller_vacation_mode',
                                'label' => __('Seller Vacation Mode'),
                                'title' => __('Seller Vacation Mode'),
                                'value' => $userFactory['seller_vacation_mode'],
                                'values' => ['1' => 'Enabled','0' => 'Disabled'],
                                'style' => 'display:none;',
                                'after_element_html' => '<b>'.$userFactory['seller_vacation_mode'] ? 'Enabled':'Disabled'.'</b>',
                        ]
            );
        } else {
            $fieldset->addField(
                'seller_vacation_mode',
                'select',
                [
                            'name' => 'seller_vacation_mode',
                            'label' => __('Seller Vacation Mode'),
                            'title' => __('Seller Vacation Mode'),
                            'value' => $userFactory['seller_vacation_mode'],
                            'values' => ['1' => 'Enabled','0' => 'Disabled'],
                                
                    ]
            );
        }
 
        $form->setValues($userFactory);
        $this->setForm($form);
        unset($userFactory['password']);
        unset($userFactory[self::CURRENT_USER_PASSWORD_FIELD]);
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
