<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ProductLabel
 * @author   ThaoPV <thaopw@gmail.com>
 */
namespace Magenest\ProductLabel\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Position
 *
 * @package Magenest\ProductLabel\Model\Config\Source
 */
class Position implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'top-left', 'label' => __('Top-Left')],
            ['value' => 'top-mid', 'label' => __('Top-Mid')],
            ['value' => 'top-right', 'label' => __('Top-Right')],
            ['value' => 'mid-left', 'label' => __('Mid-Left')],
            ['value' => 'mid-mid', 'label' => __('Mid-Mid')],
            ['value' => 'mid-right', 'label' => __('Mid-Right')],
            ['value' => 'bot-left', 'label' => __('Bottom-Left')],
            ['value' => 'bot-mid', 'label' => __('Bottom-Mid')],
            ['value' => 'bot-right', 'label' => __('Bottom-Right')]


        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'top-left' => __('Top-Left'),
            'top-mid' => __('Top-Mid'),
            'top-right' => __('Top-Right'),
            'mid-left' => __('Mid-Left'),
            'mid-mid' => __('Mid-Mid'),
            'mid-right' => __('Mid-Right'),
            'bottom-left' => __('Bottom-Left'),
            'bottom-mid' => __('Bottom-Mid'),
            'bottom-right' => __('Bottom-Right')

        ];
    }
}
