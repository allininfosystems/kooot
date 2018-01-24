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
 
namespace Medma\Marketplace\Model\System\Config\Source;

class ShopInfo implements \Magento\Framework\Option\ArrayInterface
{
   
    public function toOptionArray()
    {
        return [['value' => 'left', 'label' => __('Left Sidebar')],
                ['value' => 'right', 'label' => __('Right Sidebar')],
                ['value' => 'product_info', 'label' => __('Product Info')]
               ];
    }
}
