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
 
class Transaction extends \Magento\Framework\Model\AbstractModel
{
    const CREDIT = 'Credit';
    const DEBIT = 'Debit';
    protected function _construct()
    {
        
        $this->_init('Medma\MarketPlace\Model\ResourceModel\Transaction');
    }
}
