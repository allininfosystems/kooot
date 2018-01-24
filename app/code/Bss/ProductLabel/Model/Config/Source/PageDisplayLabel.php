<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ProductLabel
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ProductLabel\Model\Config\Source;

class PageDisplayLabel
{
    /**
     * @return array
     */
    public static function toOptionArray()
    {
        return [['value' => 'productpage', 'label'=>__('Product Page')],['value' => 'productlist', 'label'=>__('Product List Page')]];
    }
}