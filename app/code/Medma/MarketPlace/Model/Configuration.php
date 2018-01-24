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
 
namespace Medma\MarketPlace\Model;
 
class Configuration extends \Magento\Framework\Model\AbstractModel
{

    protected function _construct()
    {
        
        $this->_init('Medma\MarketPlace\Model\ResourceModel\Configuration');
    }
}
