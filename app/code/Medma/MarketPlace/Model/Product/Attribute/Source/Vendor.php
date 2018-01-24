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

namespace Medma\MarketPlace\Model\Product\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

class Vendor extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    protected $options;
    protected $rolesFactory;
    protected $marketHelper;
    protected $authSession;
    protected $UserFactory;
    
    public function __construct(
        \Magento\Authorization\Model\RoleFactory $rolesFactory,
        \Medma\MarketPlace\Helper\Data $marketHelper,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\User\Model\UserFactory $UserFactory
    ) {
        $this->rolesFactory = $rolesFactory;
        $this->marketHelper = $marketHelper;
        $this->authSession = $authSession;
        $this->UserFactory = $UserFactory;
    }

    public function getAllOptions()
    {
        $options = [];
        
        $roleId = $this->marketHelper->getConfig('general', 'vendor_role');
        
        if ($roleId) {
            $current_user = $this->authSession->getUser();
            
            $role=$this->rolesFactory->create()->load($roleId);
            
            if ($current_user->getRole()->getRoleId() == $role->getRoleId()) {
                $options[] = ['value' => $current_user->getId(), 'label' => $current_user->getName()];
            } else {
                $options[] = ['label' => __('-- Please Select --'), 'value' => ''];
                
                $roleUsers = $this->rolesFactory->create()->load($roleId)->getRoleUsers();
                
                $collection = $this->UserFactory->create()->getCollection()
                    ->addFieldToFilter('is_active', 1)
                    ->addFieldToFilter('user_id', ['in' => $roleUsers]);
                if ($collection->getSize()>0) {
                    $userIds = [];
                    foreach ($collection->getData() as $roleData) {
                        $options[] = ['value' => $roleData["user_id"], 'label' => $roleData["firstname"].' '.$roleData['lastname']];
                    }
                }
            }
        }
        return $options;
    }
}
