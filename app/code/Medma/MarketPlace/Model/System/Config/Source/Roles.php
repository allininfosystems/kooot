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

namespace Medma\MarketPlace\Model\System\Config\Source;

class Roles implements \Magento\Framework\Option\ArrayInterface
{
    protected $_options;
    protected $_rolesFactory;
    
    public function __construct(\Magento\Authorization\Model\ResourceModel\Role\Grid\CollectionFactory $rolesFactory)
    {
        $this->_rolesFactory = $rolesFactory;
    }

    public function toOptionArray()
    {
        $result = [];
        $roleCollection = $this->_rolesFactory->create()->load();
           
        foreach ($roleCollection as $role) {
            $result[] = ['value' => $role->getId(), 'label' => $role->getRoleName()];
        }
        return $result;
    }
}
