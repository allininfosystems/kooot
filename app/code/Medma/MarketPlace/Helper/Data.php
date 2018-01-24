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
 
namespace Medma\MarketPlace\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $scopeConfig;
    protected $directory_list;
    protected $listsInterface;
    protected $prooftypeFactory;
    protected $listProduct;
    
    
    const XML_PATH_EMAIL_REGISTER_TEMPLATE_FIELD  = 'marketplace/vendor_registration_email/email_template';
    
    const XML_PATH_EMAIL_REGISTER_CONFIRM_TEMPLATE_FIELD = 'marketplace/registration_confirmation_email/email_template';
    
    const XML_PATH_EMAIL_ACTIVATION_TEMPLATE_FIELD  = 'marketplace/vendor_activation_email/email_template';
       /**
        * @var \Magento\Framework\App\Config\ScopeConfigInterface
        */

    protected $_storeManager;
 
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;
 
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
     
    /**
     * @var string
     */
    protected $temp_id;
    
    protected $profile;
    protected $customerFactory;
    protected $userfactory;
    protected $profilefactory;
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Config\Model\Config\Factory $scopeConfig,
        \Magento\Directory\Model\Country $countryCollectionFactory,
        \Magento\Framework\Locale\ListsInterface $ListsInterface,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        \Magento\Catalog\Block\Product\ListProduct $listProduct,
        \Medma\MarketPlace\Model\ProoftypeFactory $prooftypeFactory,
        \Medma\MarketPlace\Model\ProfileFactory $profile,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\User\Model\UserFactory $userfactory,
        \Medma\MarketPlace\Model\ProfileFactory $profilefactory,
        \Medma\MarketPlace\Model\ConfigurationFactory $configurationFactory,
        \Magento\Backend\Helper\Data $HelperBackend
    ) {
    
        $this->scopeConfig = $scopeConfig;
        $this->prooftypeFactory = $prooftypeFactory;
        $this->directory_list = $directory_list;
        $this->listsInterface = $ListsInterface;
        $this->listProduct = $listProduct;
        $this->profile = $profile;
        $this->customerFactory = $customerFactory;
         $this->userfactory = $userfactory;
        $this->_storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
         $this->profilefactory = $profilefactory;
         $this->configurationFactory = $configurationFactory;
         $this->HelperBackend = $HelperBackend;
        parent::__construct($context);
    }
    
    public function getConfig($group, $field)
    {
        return $this->scopeConfig->getValue('marketplace/' . $group . '/' . $field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function checkSellerInFavorite($vendorId, $customerId)
    {
        $profilemodel = $this->profile->create();
        $profileModel = $profilemodel->load($vendorId);
        $favourites = $profileModel->getFavourites();
                        
        if (!is_null($favourites) && !empty($favourites)) {
            $favourites = json_decode($favourites, true);
            if (($key = array_search($customerId, $favourites)) !== false) {
                return true;
            }
        }
        return false;
    }
    
    public function getLoggedInUser()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        if ($customerSession->isLoggedIn()) {
            return $customerSession->getId();
        }
        return false;
    }
    public function getLoggedInAdminUser()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $adminSession = $objectManager->get('Magento\Backend\Model\Auth\Session');
        return $adminSession->getUser()->getRole()->getRoleId();
    }
    public function getLoggedInAdminUserId()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $adminSession = $objectManager->get('Magento\Backend\Model\Auth\Session');
        return $adminSession->getUser()->getId();
    }
    public function getCustomerName($customer_id)
    {
        $customer = $this->customerFactory->create()->load($customer_id)->getName();
        return $customer;
    }
    
    public function getMediaPath($type)
    {
        if ($type=="media") {
            $object_manager = \Magento\Framework\App\ObjectManager::getInstance();
            $storeManager = $object_manager->get('Magento\Store\Model\StoreManagerInterface');
            $currentStore = $storeManager->getStore();
            return  $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        }
    }
    
    public function getDir($type)
    {
        if ($type=="root") {
            return $this->directory_list->getRoot();
        } elseif ($type=="media") {
            return $this->directory_list->getPath('media');
        } elseif ($type=="app") {
            return $this->directory_list->getPath('app');
        } elseif ($type=="etc") {
            return $this->directory_list->getPath('etc');
        } elseif ($type=="lib_internal") {
            return $this->directory_list->getPath('lib_internal');
        } elseif ($type=="lib_web") {
            return $this->directory_list->getPath('lib_web');
        } elseif ($type=="pub") {
            return $this->directory_list->getPath('pub');
        } elseif ($type=="static") {
            return $this->directory_list->getPath('static');
        } elseif ($type=="var") {
            return $this->directory_list->getPath('var');
        } elseif ($type=="tmp") {
            return $this->directory_list->getPath('tmp');
        } elseif ($type=="pub") {
            return $this->directory_list->getPath('cache');
        } elseif ($type=="log") {
            return $this->directory_list->getPath('log');
        } elseif ($type=="session") {
            return $this->directory_list->getPath('session');
        } elseif ($type=="setup") {
            return $this->directory_list->getPath('setup');
        } elseif ($type=="di") {
            return $this->directory_list->getPath('di');
        } elseif ($type=="pub") {
            return $this->directory_list->getPath('generation');
        } elseif ($type=="upload") {
            return $this->directory_list->getPath('upload');
        } elseif ($type=="composer_home") {
            return $this->directory_list->getPath('composer_home');
        } elseif ($type=="view_preprocessed") {
            return $this->directory_list->getPath('view_preprocessed');
        } elseif ($type=="html") {
            return $this->directory_list->getPath('html');
        }
    }
    
    public function getImagesDir($type)
    {
        $path = $this->getDir("media"). '/marketplace/vendor/'.$type . '/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
            
        return $path;
    }
    
    
    public function getImagesUrl($type)
    {
        return $this->getMediaPath('media') . 'marketplace/vendor/'.$type.'/' ;
    }
    
    public function switchTemplate()
    {
        
        $configValue = $this->marketHelper->getConfig("general", "product_view_layout");
        switch ($configValue) {
            case 'empty';
                return 'page/empty.phtml';
            case 'one_column';
                return 'page/1column.phtml';
            case 'two_columns_left';
                return 'page/2columns-left.phtml';
            case 'two_columns_right';
                return 'page/2columns-right.phtml';
            case 'three_columns';
                return 'page/3columns.phtml';
        }
        return 'page/1column.phtml';
    }
    
    public function getCountryName($code)
    {
        return $this->listsInterface->getCountryTranslation($code);
    }
    
    public function getVarificationProofTypeList()
    {
        $proofType=[];
                
        $prooftypeCollection = $this->prooftypeFactory->create()->getCollection()->addFieldToFilter('status', 1);

        foreach ($prooftypeCollection as $prooftype) {
            $proofType[$prooftype->getEntityId()] = $prooftype->getName();
        }
        
        return $prooftypeCollection;
    }
    
    public function getVendorProductCollection($id)
    {
        return $this->listProduct->getLoadedProductCollection()
                                ->addFieldToFilter('status', 1)
                                ->addAttributeToFilter('vendor', $id)
                                ->addAttributeToFilter('approved', 1);
    }
    
    
    public function checkIsProductInfoLayout()
    {
        $position = $this->getConfig("general", "shop_info_display");
        if ($position == 'product_info') {
            return 1;
        } else {
            return 0;
        }
    }
    
    public function checkIsLeftRightLayout()
    {
        $position = $this->getConfig("general", "shop_info_display");
        if ($position != 'product_info') {
            return 1;
        } else {
            return 0;
        }
    }
    
    
    
    
    /**
     * Return store configuration value of your template field that which id you set for template
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    protected function getConfigValue($path, $storeId)
    {
        
        $val = $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
        return $val;
    }
 
    /**
     * Return store
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }
 
    /**
     * Return template id according to store
     *
     * @return mixed
     */
    public function getTemplateId($xmlPath)
    {
        $temp_id =  $this->getConfigValue($xmlPath, $this->getStore()->getStoreId());
        
        return $temp_id;
    }
 
    /**
     * [generateTemplate description]  with template file and tempaltes variables values
     * @param  Mixed $emailTemplateVariables
     * @param  Mixed $senderInfo
     * @param  Mixed $receiverInfo
     * @return void
     */
    public function generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $template =  $this->_transportBuilder->setTemplateIdentifier($this->temp_id)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND, /* here you can defile area and
                                                                                 store of template for which you prepare it */
                        'store' => $this->_storeManager->getStore()->getId(),
                    ]
                )
                ->setTemplateVars($emailTemplateVariables)
                ->setFrom($senderInfo)
                ->addTo($receiverInfo['email'], $receiverInfo['name']);
        return $this;
    }
    public function generateTemplateAdmin($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $template =  $this->_transportBuilder->setTemplateIdentifier($this->temp_id)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_ADMINHTML, /* here you can defile area and
                                                                                 store of template for which you prepare it */
                        'store' => $this->_storeManager->getStore()->getId(),
                    ]
                )
                ->setTemplateVars($emailTemplateVariables)
                ->setFrom($senderInfo)
                ->addTo($receiverInfo['email'], $receiverInfo['name']);
        return $this;
    }
    
    /**
     * [sendInvoicedOrderEmail description]
     * @param  Mixed $emailTemplateVariables
     * @param  Mixed $senderInfo
     * @param  Mixed $receiverInfo
     * @return void
     */
    /* your send mail method*/
    public function sendRegistrationEmailToVendor($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->temp_id = $this->getTemplateId(self::XML_PATH_EMAIL_REGISTER_TEMPLATE_FIELD);
        $this->inlineTranslation->suspend();
        $this->generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo);
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }
    
    public function sendConfirmationEmail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->temp_id = $this->getTemplateId(self::XML_PATH_EMAIL_REGISTER_CONFIRM_TEMPLATE_FIELD);
        $this->inlineTranslation->suspend();
        $this->generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo);
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }
    public function sendActivationEmailToVendor($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->temp_id = $this->getTemplateId(self::XML_PATH_EMAIL_ACTIVATION_TEMPLATE_FIELD);
        $this->inlineTranslation->suspend();
        $this->generateTemplateAdmin($emailTemplateVariables, $senderInfo, $receiverInfo);
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }
    public function getProductId()
    {
        return $this->getRequest()->getParam('id');
    }
    public function getSellerRegistrationLabelConfig()
    {
        return $this->getConfig('vendor_registration', 'request_seller_label');
    }
    public function getProductConfig()
    {
        return $this->scopeConfig->getValue('productsearch/general/extension_status');
    }
    public function getVendorSearchLabelConfig()
    {
        return $this->scopeConfig->getValue('productsearch/vendor/vendorlabel');
    }
    
    public function getUser($userid)
    {
        return $this->userfactory->create()->load($userid);
    }
    public function getProfile($userid)
    {
        return $this->profilefactory->create()->getCollection()->addFieldToFilter('user_id', $userid)->getFirstItem();
    }
    /* check Notify New order Email To Vendor */

    public function getNotifyNewOrderEmail($vendorId)
    {
        $configurationValue = $this->configurationFactory->create()->getCollection()->addFieldToFilter('vendor_id',$vendorId)->getData();
        if(count($configurationValue))
        {
           $configValue = json_decode($configurationValue[0]['value'],true);
           if($configValue['notify_new_order_email'] == 1)
           {
              return true;
           }
        }
        return false;
    }
    public function getSellerUrl()
    {
        return $this->HelperBackend->getHomePageUrl();
    }
}
