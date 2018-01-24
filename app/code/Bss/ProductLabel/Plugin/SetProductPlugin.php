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
namespace Bss\ProductLabel\Plugin;

use Bss\ProductLabel\Block\Label;

class SetProductPlugin
{
    /**
     * @var Label
     */
    protected $label;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * ImageBlockBuilderPlugin constructor.
     * @param Label $label
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(Label $label, \Magento\Framework\Registry $registry)
    {
        $this->label=$label;
        $this->registry = $registry;
    }

    /**
     * @param \Magento\Catalog\Model\Product $subject
     * @param callable $proceed
     * @return mixed
     */
    public function aroundGetProductUrl(
        $subject,
        callable $proceed
    ) {
        $returnValue = $proceed();
        $this->registry->unregister('current_label_product');
        $this->registry->register('current_label_product', $subject);
        return $returnValue;
    }
}