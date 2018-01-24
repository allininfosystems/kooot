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

class Profile extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
            $model['shop_name']='';
            $model['message']='';
            $model['contact_number']='';
            $model['country']='';
            $model['display_profile_frontend_for_vendor']='';
            $model['display_profile_frontend_for_admin']='';
            $model['proof_type']='';
            $model['admin_commission_type']='';
            $model['total_admin_commission']='';
            $model['total_vendor_amount']='';
        }

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Profile Information')]);

        
        $fieldset->addField(
            'shop_name',
            'text',
            [
                                    'name' => 'shop_name',
                                    'label' => __('Shop Name'),
                                    'title' => __('Shop Name'),
                                    'value' => $model['shop_name'],
                                    'required' => true,
                            ]
        );
        $fieldset->addField(
            'message',
            'textarea',
            [
                                    'name' => 'message',
                                    'label' => __('Shop Description'),
                                    'value' => $model['message'],
                                    'title' => __('Message'),
                                    ]
        );
        $fieldset->addField(
            'contact_number',
            'text',
            [
                                    'name' => 'contact_number',
                                    'label' => __('Contact Number'),
                                    'value' => $model['contact_number'],
                                    'title' => __('Contact Number'),
                                    'required' => true,
                            ]
        );
        $fieldset->addField(
            'country',
            'select',
            [
                                    'name' => 'country',
                                    'label' => __('Country'),
                                    'title' => __('Country'),
                                    'value' => $model['country'],
                                    'required' => true,
                                    'values' => $countries
                            ]
        );
        if ($userId) {
            $fieldset->addField(
                'image',
                'file',
                [
                                        'name' => 'image',
                                        'label' => __('Profile Picture'),
                                        'title' => __('Profile Picture'),
                                        'after_element_html' => '<br />' . $this->_getImage($model->getImage())
                                    ]
            );
        } else {
            $fieldset->addField(
                'image',
                'file',
                [
                                        'name' => 'image',
                                        'label' => __('Profile Picture'),
                                        'title' => __('Profile Picture')
                                ]
            );
        }
        
        $roleId = $this->vendorHelper->getConfig('general', 'vendor_role');
        //$current_user = $this->adminSession->getUser();
        if ($current_user->getRole()->getRoleId() == $roleId) {
            $fieldset->addField(
                'display_profile_frontend_for_vendor',
                'select',
                [
                                      'name' => 'display_profile_frontend_for_vendor',
                                      'label' => __('Display Profile Information At Frontend'),
                                      'title' => __('Display Profile Information At Frontend'),
                                      'value' => $model['display_profile_frontend_for_vendor'],
                                      'required' => true,
                                      'values' => ['1' => 'Yes', '0' => 'No']
                              ]
            );
        } else {
            $fieldset->addField(
                'display_profile_frontend_for_admin',
                'select',
                [
                                      'name' => 'display_profile_frontend_for_admin',
                                      'label' => __('Display Profile Information At Frontend'),
                                      'value' => $model['display_profile_frontend_for_admin'],
                                      'title' => __('Display Profile Information At Frontend'),
                                      'required' => true,
                                      'values' => ['0' => 'No','1' => 'Yes']
                              ]
            );
        }
        
        $proofList = $this->vendorHelper->getVarificationProofTypeList();
        if (count($proofList) > 0) {
            if ($userId) {
                $fieldset->addField(
                    'proof_type',
                    'text',
                    [
                                    'name' => 'proof_type',
                                    'label' => __('Proof Type'),
                                    'title' => __('Proof Type'),
                                    'value' => $model['proof_type'],
                                    'style' => 'display:none',
                                    'after_element_html' => $this->_getFiles($model->getProofType(), $model->getVarificationFiles())
                                    ]
                );
            } else {
                $fieldset->addField(
                    'proof_type',
                    'text',
                    [
                                'name' => 'proof_type',
                                'label' => __('Proof Type'),
                                'value' => $model['proof_type'],
                                'title' => __('Proof Type'),
                                'style' => 'display:none',
                                ]
                );
            }
        }
        $admin_commission_type = $fieldset->addField(
            'admin_commission_type',
            'select',
            [
                                    'name' => 'admin_commission_type',
                                    'label' => __('Admin Commission Type'),
                                    'value' => $model['admin_commission_type'],
                                    'title' => __('Admin Commission Type'),
                                    'required' => true,
                                    'values'    =>[''=>'Please Select','1' => 'Currency Amount', '2' => 'Percentage']
                                ]
        );
        $admin_commission_flat = $fieldset->addField(
            'admin_commission_flat',
            'text',
            [
                            'name' => 'admin_commission_flat',
                            'label' => __('Admin Commission Flat'),
                            'title' => __('Admin Commission Flat'),
                            'required' => true,
                            'class' => 'validate-number',
                             'display' => 'none'
                             ]
        );
        $admin_commission_presentage = $fieldset->addField(
            'admin_commission_percentage',
            'text',
            [
                            'name' => 'admin_commission_percentage',
                            'label' => __('Commission (in %)'),
                            'title' => __('Commission (in %)'),
                            'required' => true,
                            'class' => 'validate-number',
                             'display' => 'none'
                             ]
        );
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
                      ->addFieldMap($admin_commission_type->getHtmlId(), $admin_commission_type->getName())
                      ->addFieldMap($admin_commission_flat->getHtmlId(), $admin_commission_flat->getName())
                      ->addFieldMap($admin_commission_presentage->getHtmlId(), $admin_commission_presentage->getName())
                      ->addFieldDependence(
                          $admin_commission_presentage->getName(),
                          $admin_commission_type->getName(),
                          2
                      )
                            ->addFieldDependence(
                                $admin_commission_flat->getName(),
                                $admin_commission_type->getName(),
                                1
                            )
        );
        $fieldset->addField(
            'total_sale',
            'label',
            [
                                'name' => 'total_sale',
                                'label' => __('Total Sale'),
                                'title' => __('Total Sale'),
                            ]
        );
        if ($userId) {
            $fieldset->addField(
                'total_admin_commission',
                'text',
                [
                                'name' => 'total_admin_commission',
                                'label' => __('Total Commission Paid/Due'),
                                'title' => __('Total Commission Paid/Due'),
                                'style' => 'display:none;',
                                'value' => $model->getTotalAdminCommission(),
                                'after_element_html' => '<b>'.$this->formatPrice($model->getTotalAdminCommission()).'</b>',
                                 ]
            );
        } else {
            $fieldset->addField(
                'total_admin_commission',
                'text',
                [
                                'name' => 'total_admin_commission',
                                'label' => __('Total Commission Paid/Due'),
                                'title' => __('Total Commission Paid/Due'),
                                'style' => 'display:none;',
                                'after_element_html' => '<b>'.$this->formatPrice(0).'</b>',
                                ]
            );
        }
        if ($userId) {
            $fieldset->addField(
                'total_vendor_amount',
                'text',
                [
                                'name' => 'total_vendor_amount',
                                'label' => __('Total Income After Commission'),
                                'title' => __('Total Income After Commission'),
                                'style' => 'display:none;',
                                'value' => $model->getTotalVendorAmount(),
                                'after_element_html' => '<b>'.$this->formatPrice($model->getTotalVendorAmount()).'</b>',
                                 ]
            );
        } else {
            $fieldset->addField(
                'total_vendor_amount',
                'text',
                [
                                'name' => 'total_vendor_amount',
                                'label' => __('Total Income After Commission'),
                                'title' => __('Total Income After Commission'),
                                'style' => 'display:none;',
                                'after_element_html' => '<b>'.$this->formatPrice(0).'</b>',
                                ]
            );
        }
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
        return __('Profile Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Profile Information');
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
    
    protected function _getImage($image_name)
    {
        if (!isset($image_name) || $image_name == '') {
            return '';
        }
            
       // $dir_name = 'vendor' . DS . 'images';
        $dir_path = $this->vendorHelper->getImagesUrl('profile');

        $str = '<img width="150" src="' . $dir_path . $image_name . '" alt="" style="margin-top: 10px;" /><input type="hidden" name="old_image" id="old_image" value="' . $image_name . '" />';
        $str .= '<br /><a href="javascript:void(0)" onclick="$(this).previous().remove(); $(this).previous().remove();$(this).previous().remove();$(this).previous().remove();$(this).remove();$(\'#old_image\').remove();">Remove</a>';
        return $str;
    }
    
    protected function _getFiles($proofType, $fileList)
    {
        $fileListArray = json_decode($fileList, true);
        $proofTypeModel = $this->prooftypeFactory->create()->load($proofType);
        $proofName = $proofTypeModel->getName();
        if (isset($proofName)) {
            $fileString = '<div style="margin: 0 0 1px;"><b>' . $proofName . '</b></div>';
            foreach ($fileListArray as $file) {
            //$dir_name = 'vendor' . DS . 'varifications';
                $dir_url = $this->vendorHelper->getImagesUrl('varifications');
                            
                $fileString .= '<div style="margin: 2px 0 1px;"><a href="' . $dir_url . $file . '" target="_blank">' . $file . '</a></div>';
            }
        } else {
            $fileString = '<div style=""><b>N/A</b></div>';
        }

        return $fileString;
    }
    protected function formatPrice($price)
    {
        return $this->pricehelper->currency($price, true, false);
    }
}
