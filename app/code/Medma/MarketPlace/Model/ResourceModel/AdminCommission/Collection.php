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
 
namespace Medma\MarketPlace\Model\ResourceModel\AdminCommission;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define model & resource model
     */

    
    protected function _construct()
    {
        $this->_init('Medma\MarketPlace\Model\AdminCommission', 'Medma\MarketPlace\Model\ResourceModel\AdminCommission');
      
    }
}
