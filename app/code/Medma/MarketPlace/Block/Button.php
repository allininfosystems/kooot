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

namespace Medma\MarketPlace\Block;
 
class Button extends \Magento\Framework\View\Element\Template
{
   
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context
    ) {
        parent::__construct($context);
    }
}
