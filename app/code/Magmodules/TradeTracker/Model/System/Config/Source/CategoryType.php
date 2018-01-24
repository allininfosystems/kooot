<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\TradeTracker\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class CategoryType
 *
 * @package Magmodules\TradeTracker\Model\System\Config\Source
 */
class CategoryType implements ArrayInterface
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
                ['value' => 'include', 'label' => __('Include by Category')],
                ['value' => 'exclude', 'label' => __('Exclude by Category')]
            ];
        }
        return $this->options;
    }
}
