<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\TradeTracker\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class CategorySource
 *
 * @package Magmodules\TradeTracker\Model\System\Config\Source
 */
class CategorySource implements ArrayInterface
{

    /**
     * Options array
     *
     * @var array
     */
    public $options = null;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [
                ['value' => '', 'label' => __('Magento Category Tree')],
                ['value' => 'custom', 'label' => __('Custom Category Value')],
                ['value' => 'attribute', 'label' => __('Use Attribute')],
            ];
        }
        return $this->options;
    }
}
