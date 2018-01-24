<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\TradeTracker\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Conditions
 *
 * @package Magmodules\TradeTracker\Model\System\Config\Source
 */
class Conditions implements ArrayInterface
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
                [
                    'value' => '',
                    'label' => __('')
                ],
                [
                    'value' => 'eq',
                    'label' => __('Equal')
                ],
                [
                    'value' => 'neq',
                    'label' => __('Not equal')
                ],
                [
                    'value' => 'gt',
                    'label' => __('Greater than')
                ],
                [
                    'value' => 'gteq',
                    'label' => __('Greater than or equal to')
                ],
                [
                    'value' => 'lt',
                    'label' => __('Less than')
                ],
                [
                    'value' => 'lteg',
                    'label' => __('Less than or equal to')
                ],
                [
                    'value' => 'in',
                    'label' => __('In')
                ],
                [
                    'value' => 'nin',
                    'label' => __('Not in')
                ],
                [
                    'value' => 'like',
                    'label' => __('Like')
                ],
                [
                    'value' => 'empty',
                    'label' => __('Empty')
                ],
                [
                    'value' => 'not-empty',
                    'label' => __('Not Empty')
                ],
            ];
        }
        return $this->options;
    }
}
