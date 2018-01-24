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
 
namespace Medma\MarketPlace\Controller\Adminhtml\Vendor;

use Magento\Authorization\Model\UserContextInterface as UserContextInterface;
use Magento\Authorization\Model\Acl\Role\User as RoleGroup;

class Save extends \Magento\Backend\App\Action
{
    protected $ProfileFactory;
    protected $authSession;
    protected $rolesFactory;
    protected $rulesFactory;
    protected $adminuser;
    protected $vendorHelper;
    protected $_fileUploaderFactory;
    protected $coreregistry;
    protected $adminSession;
    protected $session;
    protected $productRepository;
    protected $productCollection;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Medma\MarketPlace\Model\ProfileFactory $ProfileFactory,
        \Magento\Authorization\Model\RoleFactory $rolesFactory,
        \Magento\Authorization\Model\RulesFactory $rulesFactory,
        \Magento\User\Model\UserFactory $adminuser,
        \Medma\MarketPlace\Helper\Data $vendorHelper,
        \Magento\MediaStorage\Model\File\UploaderFactory $_fileUploaderFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Session\SessionManager $session,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
    ) {
        $this->productCollection  = $productCollection;
        $this->productRepository = $productRepository;
        $this->ProfileFactory = $ProfileFactory;
        $this->_fileUploaderFactory = $_fileUploaderFactory;
        $this->adminuser = $adminuser;
        $this->adminSession = $adminSession;
        $this->rolesFactory = $rolesFactory;
        $this->rulesFactory = $rulesFactory;
        $this->vendorHelper = $vendorHelper;
        $this->coreregistry = $registry;
        $this->session = $session;
        parent::__construct($context);
    }
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        $this->session->setUserData($data);
        $userId = $this->getRequest()->getParam('id');
        $adminuser = $this->adminuser->create();
        $wasActive = $adminuser->load($userId)->getIsActive();

        //$wasActive = $userFactory['is_active'];
        if (!$userId) {
            $cur_admin_user_role_name = $this->adminSession->getUser()->getRole()->getRoleName();

            if ($cur_admin_user_role_name == "VENDORS") {
                $userId = $current_user = $this->adminSession->getUser()->getId();
            } else {
                $userId = null;
            }
        }
         
         /***************** Set Data in admin user table   *******************/
         
         
        if ($data = $this->getRequest()->getPost()) {
            try {
            if ($data['is_active']=='1' || $data['is_active']==null){
                    $products = $this->productCollection->load()->addFieldToFilter('vendor', $userId);
		    if(!empty($products->getData()))
		{
                    foreach ($products as $product) {
                        $current_product =$this->productRepository->get($product->getSku());
                        if ($current_product->getVendor()==$userId) {
                            $current_product = $this->productRepository->get($product->getSku());
                            $current_product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                            $this->productRepository->save($current_product);
                        }
                    }
		}
                } else {
                        $products = $this->productCollection->load()->addFieldToFilter('vendor', $userId);
		if(!empty($products->getData()))
		{
                    foreach ($products as $product) {
                        $current_product =$this->productRepository->get($product->getSku());
                        if ($current_product->getVendor()==$userId) {
                            $current_product = $this->productRepository->get($product->getSku());
                            $current_product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                            $this->productRepository->save($current_product);
                        }
                    }
		}
                }
                if ($userId) {
                    
                    $adminuser = $adminuser->load($userId)
                                           ->setUsername($data['username'])
                                           ->setFirstname($data['firstname'])
                                           ->setLastname($data['lastname'])
                                           ->setEmail(strtolower($data['email']))
                                           ->setIsActive($data['is_active'])
                                           ->setSellerVacationMode($data['seller_vacation_mode']);
                    if ($this->getRequest()->getParam('password', false)) {
                        $adminuser->setPassword($this->getRequest()->getParam('password', false));
                    }
                    if ($this->getRequest()->getParam('confirmation', false)) {
                        $adminuser->setPasswordConfirmation($this->getRequest()->getParam('confirmation', false));
                    }
                       $result = $adminuser->validate();
                    if (is_array($result)) {
                        foreach ($result as $message) {
                            $this->messageManager->addError($result[0]->getText());
                        }
                        $this->_redirect($this->_redirect->getRefererUrl());
                        return;
                    }
                } else {
                    $adminuser = $adminuser->setUsername($data['username'])
                                         ->setFirstname($data['firstname'])
                                         ->setLastname($data['lastname'])
                                         ->setEmail(strtolower($data['email']))
                                         ->setIsActive($data['is_active'])
                                         ->setSellerVacationMode($data['seller_vacation_mode']);
                    if ($data['confirmation']) {
                        if ($this->getRequest()->getParam('password', false)) {
                                $adminuser->setPassword($this->getRequest()->getParam('password', false));
                        }
                        if ($this->getRequest()->getParam('confirmation', false)) {
                                $adminuser->setPasswordConfirmation($this->getRequest()->getParam('confirmation', false));
                        }
                        $result = $adminuser->validate();
                        if (is_array($result)) {
                            foreach ($result as $message) {
                                  $this->messageManager->addError($message);
                            }
                            $this->_redirect($this->_redirect->getRefererUrl());
                            return;
                        }
                    }
                }
                try {
                    $adminuser->save();
                } catch (\Magento\Framework\Validator\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $this->_redirect($this->_redirect->getRefererUrl());
                    return;
                }
                    
                    
                
             /**************************** Admin user Data saved successfully  *******************/
             
             /**************************** Set Role "Vendors"   *************************************/
                
                $role_id = $this->vendorHelper->getConfig('general', 'vendor_role');
                $role=$this->rolesFactory->create();
                $role->setRoleName($data['username'])
                        ->setRoleType(RoleGroup::ROLE_TYPE)
                        ->setParentId($role_id)
                        ->setTreeLevel(2)
                        ->setUserId($adminuser->getUserId())
                        ->setUserType(UserContextInterface::USER_TYPE_ADMIN);
                $role->save();
                /********************Set Role "Vendors" saved successfully  *******************/
                 
                $image=null;
                
                $dir_path = $this->vendorHelper->getImagesDir('profile');
                
                try {
                    if ($this->_fileUploaderFactory->create(['fileId' => 'image'])) {
                        $uploader = $this->_fileUploaderFactory->create(['fileId' => 'image']);
                        $upload_file = $uploader->validateFile();
                        if (isset($upload_file['name']) && $upload_file['name'] != '') {
                       //	$new_file['name'] = date("YmdHis")."-".$upload_file['name'];
                            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'image']);
                          
                            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                          
                            $uploader->setAllowRenameFiles(false);
                          
                            $uploader->setFilesDispersion(false);
                     
                            $uploader->save($dir_path);
                            $image = $upload_file['name'];
                        }
                    }
                } catch (\Exception $e) {
                    if (isset($data['old_image']) && !empty($data['old_image'])) {
                         $image = $data['old_image'];
                    } else {
                        $image = '';
                    }
                }
                     
                
                
              /**************************** Set Profile Data   **************************************/
                $ProfileFactory=$this->ProfileFactory->create();
                
                 $admin_user_id = $adminuser->load($userId)->getUserId();
                     
                     
                     
                     $profileCollection=$ProfileFactory->getCollection()->addFieldToFilter('user_id', $admin_user_id);
                         
                    // $profileCollection->getFirstItem()->getId();
                if ($profileCollection->count() > 0) {
                     $profile = $ProfileFactory->load($profileCollection->getFirstItem()->getId());
    
                    if (!is_null($image)) {
                        $profile->setImage($image);
                    }
                             
                    /*
                   * Send Email
                   * */
                                
                    $receiverInfo = [
                      'name' => $data['shop_name'],
                      'email' => $data['email']
                    ];
                                
                               
                  /* Sender Detail  */
             
                if($data['is_active']=='1' &&  $wasActive == '0')
                {            
                    $sender = $this->vendorHelper->getTemplateId('marketplace/vendor_activation_email/email_sender');
                    $sendername = $this->vendorHelper->getTemplateId('trans_email/ident_'.$sender.'/name');
                    $senderemail = $this->vendorHelper->getTemplateId('trans_email/ident_'.$sender.'/email');
                                
                                
                    $senderInfo = [
                      'name' => $sendername,
                      'email' => $senderemail,
                    ];
                                
                    $emailTemplateVariables = [];
                    $emailTempVariables['myvar1'] = $data['firstname'];
                                
                     try{


                     $this->vendorHelper->sendActivationEmailToVendor(
                          $emailTempVariables,
                          $senderInfo,
                          $receiverInfo
                      );
                    }catch(\Exception $e)
                     {
                        
                     }
                      }           
                    $profile->setShopName($data['shop_name'])
                         ->setUserId($adminuser->getUserId())
                          ->setMessage($data['message'])
                          ->setContactNumber($data['contact_number'])
                          ->setCountry($data['country']);
                          
                    if ($data['display_profile_frontend_for_admin']) {
                        $profile->setDisplayProfileFrontendForAdmin($data['display_profile_frontend_for_admin']);
                    } else {
                        $profile->setDisplayProfileFrontendForVendor($data['display_profile_frontend_for_vendor']);
                    }

                      $profile->setAdminCommissionType($data['admin_commission_type'])
                          ->setAdminCommissionFlat($data['admin_commission_flat'])
                          ->setAdminCommissionPercentage($data['admin_commission_percentage'])
                          ->setClientEmailId($data['client_email_id'])
                          ->setClientId($data['client_id'])
                          ->setClientSecret($data['client_secret'])
                          ->setStripeEmailId($data['stripe_email_id'])
                          ->setStripeSecretApiKey($data['stripe_secret_api_key'])
                          ->setStripePublishableApiKey($data['stripe_publishable_api_key'])
                          ->save();
                    if (!($this->getRequest()->getParam('id'))) {
                        $this->_redirect('*/*/edit');
                    } else {
                          $this->_redirect('*/*/edit', ['id' =>$userId]);
                    }
                      $this->messageManager->addSuccess(_('Data Updated successfully'));
                      $this->session->setUserData('');
    
                      
                  
                } else {
                    $ProfileFactory->setTotalAdminCommission(0)
                                            ->setTotalVendorAmount(0)
                                            ->setTotalVendorPaid(0);
                                                        
                    if (!is_null($image)) {
                        $ProfileFactory->setImage($image);
                    }
                                    
                         $ProfileFactory->setShopName($data['shop_name'])
                            ->setUserId($adminuser->getUserId())
                            ->setMessage($data['message'])
                            ->setContactNumber($data['contact_number'])
                            ->setCountry($data['country']);
                    if ($data['display_profile_frontend_for_admin']) {
                        $ProfileFactory->setDisplayProfileFrontendForAdmin($data['display_profile_frontend_for_admin']);
                    } else {
                        $ProfileFactory->setDisplayProfileFrontendForVendor($data['display_profile_frontend_for_vendor']);
                    }
                    if ($data['sell_products_on_installment_for_admin']) {
                        $ProfileFactory->setSellProductsOnInstallmentForAdmin($data['sell_products_on_installment_for_admin']);
                    } else {
                        $ProfileFactory->setSellProductsOnInstallmentForVendor($data['sell_products_on_installment_for_vendor']);
                    }
                        $ProfileFactory->setAdminCommissionType($data['admin_commission_type'])
                            ->setAdminCommissionFlat($data['admin_commission_flat'])
                            ->setAdminCommissionPercentage($data['admin_commission_percentage'])
                            ->setClientEmailId($data['client_email_id'])
                            ->setClientId($data['client_id'])
                            ->setClientSecret($data['client_secret'])
                            ->setStripeEmailId($data['stripe_email_id'])
                            ->setStripeSecretApiKey($data['stripe_secret_api_key'])
                            ->setStripePublishableApiKey($data['stripe_publishable_api_key'])
                            ->save();
                        $this->_redirect('*/*/index', ['_current' => true]);
                        $this->messageManager->addSuccess(_('Data saved successfully'));
                        $this->session->setUserData('');
                }
                    
                      
                        
              /****************************  Profile Data saved successfully  *******************/
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect($this->_redirect->getRefererUrl());
                return;
            }
        }
    }
}
