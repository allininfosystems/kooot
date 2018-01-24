<?php
/*
 * Copyright © 2016 Medma. All rights reserved.
 * 
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 */
namespace Medma\MarketPlace\Controller\Vendor;

use Magento\Authorization\Model\UserContextInterface as UserContextInterface;
use Magento\Authorization\Model\Acl\Role\User as RoleGroup;
use Magento\Framework\Message\ManagerInterface;
use Medma\MarketPlace\Model\ProfileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\App\Action;

class Saveprofile extends \Magento\Framework\App\Action\Action
{
  
   /**
    * @var \Magento\Config\Model\Config\Factory
    */
    protected $scopeConfig;
   
   /**
    * @var \Medma\MarketPlace\Model\ProfileFactory
    */
    protected $profile;
    
   /**
    * @var Magento\Framework\Message\ManagerInterface
    */
    protected $messageManager;
    
   /**
    * @var \Magento\Authorization\Model\RoleFactory
    */
    protected $rolesFactory;
    
   /**
    * @var \Medma\MarketPlace\Helper\Data
    */
    protected $marketHelper;
    
   /**
    * @var \Magento\MediaStorage\Model\File\UploaderFactory
    */
    protected $_fileUploaderFactory;
   
   /**
    * @var \Magento\Framework\Registry
    */
    protected $coreregistry;
   
   /**
    * @var \Magento\Framework\Session\SessionManager
    */
    protected $session;
    
    /**
     *
     * @param \Magento\Framework\App\Action\Context $context,
     * @param \Magento\Framework\Registry $coreregistry,
     * @param \Magento\User\Model\UserFactory $adminuser,
     * @param \Medma\MarketPlace\Helper\Data $marketHelper,
     * @param \Magento\Authorization\Model\RoleFactory $rolesFactory,
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
     * @param \Magento\Framework\Session\SessionManager $session,
     * @param \Medma\MarketPlace\Model\ProfileFactory $profile,
     * @param \Magento\Config\Model\Config\Factory $scopeConfig,
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     *
     */
 
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreregistry,
        \Magento\User\Model\UserFactory $adminuser,
        \Medma\MarketPlace\Helper\Data $marketHelper,
        \Magento\Authorization\Model\RoleFactory $rolesFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Session\SessionManager $session,
        ProfileFactory $profile,
        \Magento\Config\Model\Config\Factory $scopeConfig
    ) {
        $this->profile = $profile;
        $this->coreregistry = $coreregistry;
        $this->session = $session;
        $this->rolesFactory = $rolesFactory;
        $this->messageManager = $context->getMessageManager();
        $this->ModelUser = $adminuser;
        $this->marketHelper = $marketHelper;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $post = $this->getRequest()->getParams();
        
        $this->session->setVendorData($post);
        $model = $this->profile->create();
        $total_file_upload = $this->getRequest()->getParam('total_file_upload', false);
        
        if ($post) {
            try {
                $data = [];
                $data = $post;
                        
                if ($data['password']!=$data['confirmation']) {
                    $this->messageManager->addError(__("Entered confirm password do not matches the password"));
                    $this->_redirect($this->_redirect->getRefererUrl());
                    return;
                }
                
                $uploaded_files = [];
                $fileId = 'file';
                
                   /**
                 * Check files allowed
                 **/
                  
                $file_types = $this->marketHelper->getConfig('vendor_registration', 'files_allowed');
                $file_types = str_replace(' ', '', $file_types);
                $file_types_array = array_map('trim', explode(',', $file_types));
                
                
                /*
				 * Check max allowed file size
				 */


                $max_allowed_file_size = $this->marketHelper->getConfig('vendor_registration', 'max_allowed_file_size');
                $max_allowed_file_size_bytes = ($max_allowed_file_size * 1024 * 1024);
                
                
                /*
				 * UPLOAD verification proof FILES
				 */
                 
                $uploaded_files = [];
                for ($i = 1; $i <= $total_file_upload; $i++) {
                    $file_control_name = 'varification_proof_' . $i;
                    
                    $uploader = $this->_fileUploaderFactory->create(['fileId' => $file_control_name]);
                    $arr = $uploader->validateFile();
                    
                                        
                    $arr[$file_control_name]['name'] = str_replace(' ', '', $arr['name']);
                    $arr[$file_control_name]['name'] = date("YmdHis")."-".$arr['name'];
                        
                                        
                    if (isset($arr[$file_control_name]['name']) && $arr[$file_control_name]['name'] != '') {
                        if ($arr['size'] > $max_allowed_file_size_bytes) {
                            $this->messageManager->addError(__('File size should not exceed ' . $max_allowed_file_size .  ' Mb'));
                            $this->_redirect($this->_redirect->getRefererUrl());
                            return;
                        }
                    
                        try {
                            $f_type=$arr['type'];
                            if ($f_type!= "image/gif" && $f_type!= "image/png" && $f_type!= "image/jpeg" && $f_type!= "image/JPEG" && $f_type!= "image/PNG" && $f_type!= "image/GIF") {
                                $this->messageManager->addError(__('Disallowed File Type'));
                                $this->_redirect($this->_redirect->getRefererUrl());
                                return;
                            }
                    
                            $dir_path = $this->marketHelper->getImagesDir('varifications');
                                 
                            $uploader = $this->_fileUploaderFactory->create(['fileId' => $file_control_name]);
          
                            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                            $uploader->addValidateCallback('validate', $this, 'validateFile');
                        
                            $uploader->setAllowRenameFiles(false);
                              
                            $uploader->setFilesDispersion(false);
                            
                            $uploader->save($dir_path, $arr[$file_control_name]['name']);
                        } catch (\Magento\Framework\Validator\Exception $e) {
                            $this->messageManager->addError($e->getMessage());
                            $this->_redirect($this->_redirect->getRefererUrl());
                            return;
                        }
                        $uploaded_files[] = $arr[$file_control_name]['name'];
                    }
                }
                
                $roleId = $this->marketHelper->getConfig('general', 'vendor_role');

                $varification_files = json_encode($uploaded_files);
                
                
                /**
                 * Set Data in admin user table
                 *
                 **/
                $adminuser = $this->ModelUser->create();
                $adminuser = $adminuser->setUsername($data['username'])
                                       ->setFirstname($data['firstname'])
                                       ->setLastname($data['lastname'])
                                       ->setEmail(strtolower($data['email']))
                                       ->setIsActive(0);
                    
                /**
                 * Set Data in authorization table
                 *
                 **/
                                                   
                $role=$this->rolesFactory->create();
                $role->setRoleName($data['username'])
                     ->setRoleType(RoleGroup::ROLE_TYPE)
                     ->setParentId($roleId)
                     ->setTreeLevel(2)
                     ->setUserType(UserContextInterface::USER_TYPE_ADMIN);
                    
                try {
                    if ($data['password']) {
                        $adminuser->setPassword($data['password']);
                        $result = $adminuser->validate();
                        $adminuser->save();
                        $id = $adminuser->getUserId();
                        $role->setUserId($id);
                        $role->save();
                    }
                } catch (\Magento\Framework\Validator\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $this->_redirect($this->_redirect->getRefererUrl());
                    return;
                }
                    
                    
                if (is_array($result)) {
                    $this->adminSession->setUserData($data);
                    foreach ($result as $message) {
                        $this->messageManager->addError($message);
                    }
                    $this->_redirect($this->_redirect->getRefererUrl());
                    return;
                } else {
                    
                    /**
                     * Set Data in Marketplace Profile Table
                     *
                     **/
                    
                    $data["user_id"] = $id;
                    $data["shop_name"] = $post["shop_name"];
                    $data["contact_number"] = $post["contact_number"];
                    $data["country"] = $post["country"];
                    
                    
                    /**
                     * Verification Files Uploaded
                     *
                     **/
                    if (count($uploaded_files)>0) {
                        $data["proof_type"] = $post["proof_type"];
                        $data["varification_files"] = json_encode($uploaded_files);
                    }
                    

                    $data["create_date"] = date("Y-m-d H:i:s");
                    $data["total_admin_commission"] = 0;
                    $data["total_vendor_amount"] = 0;
                    $data["total_vendor_paid"] = 0;
                    
                    $model = $this->profile->create();
                      
                    $model->setData($data);
                    
                    /*
                     * Send Email
                     * */
                    
                    $receiverInfo = [
                    'name' => $data['firstname'],
                    'email' => $data['email']
                    ];
                    
                    
                    try{
                    /* Sender Detail  */
                    if ($this->marketHelper->getConfig('vendor_registration_email', 'enable_registration_email')==1) {
                         $reciever = $this->marketHelper->getTemplateId('marketplace/vendor_registration_email/email_receiver');
                         $receivername = $this->marketHelper->getTemplateId('trans_email/ident_'.$reciever.'/name');
                         $receiveremail = $this->marketHelper->getTemplateId('trans_email/ident_'.$reciever.'/email');
                         $sendername = $this->marketHelper->getTemplateId('trans_email/ident_general/name');
                         $senderemail = $this->marketHelper->getTemplateId('trans_email/ident_general/email');
                        
                        
                         $receiverInfo = [
                         'name' => $receivername,
                         'email' => $receiveremail,
                         ];
                         $senderInfo = [
                         'name' => $sendername,
                         'email' => $senderemail,
                         ];
                                                
                         $emailTemplateVariables = [];
                         $emailTempVariables['myvar1'] = $data['firstname'].' '.$data['lastname'];
                         $emailTempVariables['myvar2'] = $receivername;
                        
                         $this->marketHelper->sendRegistrationEmailToVendor(
                             $emailTempVariables,
                             $senderInfo,
                             $receiverInfo
                         );
                     
                     }
                     /** 
                      * send email to vendor
                     */
                     if ($this->marketHelper->getConfig('registration_confirmation_email', 'active_vendor_email')==1) {
                         $vendorSender = $this->marketHelper->getTemplateId('marketplace/registration_confirmation_email/email_sender');
                         $vendorsendername = $this->marketHelper->getTemplateId('trans_email/ident_'.$vendorSender.'/name');
                         $vendorsenderemail = $this->marketHelper->getTemplateId('trans_email/ident_'.$vendorSender.'/email');
                        
                         $vendorReceiverInfo = [
                         'name' => $data['firstname'],
                         'email' => $data['email']
                         ];
                         $vendorSenderInfo = [
                         'name' => $vendorsendername,
                         'email' => $vendorsenderemail,
                         ];
                                                
                         $vendorEmailTemplateVariables = [];
                         $vendorEmailTemplateVariables['vendorname'] = $data['firstname'];
                        
                         $this->marketHelper->sendConfirmationEmail(
                             $vendorEmailTemplateVariables,
                             $vendorSenderInfo,
                             $vendorReceiverInfo
                         );

                     }
                 }catch (\Exception $e) {
                        $this->messageManager->addError($e->getMessage());
                      
                  }
                    $model->save();
                    
                    $this->messageManager->addSuccess(__('Request has been sent successfully, we will contact you soon.'));
                    $this->_redirect($this->_redirect->getRefererUrl());
                }
            } catch (\Magento\Framework\Validator\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect($this->_redirect->getRefererUrl());
                return;
            }
        }
        $this->_redirect($this->_redirect->getRefererUrl());
    }
}
