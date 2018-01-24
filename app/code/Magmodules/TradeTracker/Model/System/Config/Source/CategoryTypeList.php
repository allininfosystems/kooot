<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\TradeTracker\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class CategoryTypeList
 *
 * @package Magmodules\TradeTracker\Model\System\Config\Source
 */
class CategoryTypeList implements ArrayInterface
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
                ['value' => 'in', 'label' => __('Include by Category')],
                ['value' => 'nin', 'label' => __('Exclude by Category')],
            ];
        }
        return $this->options;
    }
}
