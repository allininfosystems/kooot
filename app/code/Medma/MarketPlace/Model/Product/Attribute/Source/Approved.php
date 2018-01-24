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

class Approved extends \Magento\Eav\Model\Entity\Attribute\Source\Boolean
{
    /**
     * Value of 'Use Config' option
     */
    const VALUE_USE_CONFIG = 2;

    /**
     * Retrieve all attribute options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __('Yes'), 'value' => 1],
                ['label' => __('No'), 'value' => 0],
                ['label' => __('Rejected'), 'value' => 2],
            ];
        }
        return $this->_options;
    }
}
