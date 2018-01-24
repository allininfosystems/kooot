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

class ProductPlugin
{

    /**
     * @var Label
     */
    protected $label;

    /**
     * @var \Bss\ProductLabel\Helper\data
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * ProductPlugin constructor.
     * @param Label $label
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Bss\ProductLabel\Helper\data $helper
     */
    public function __construct(
        Label $label,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Bss\ProductLabel\Helper\Data $helper
    ) {
        $this->label=$label;
        $this->helper=$helper;
        $this->productFactory = $productFactory;
    }

    /**
     * @param \Magento\Catalog\Block\Product\View\Gallery $subject
     * @param $result
     * @return string
     */
    public function afterToHtml(\Magento\Catalog\Block\Product\View\Gallery $subject, $result)
    {
        $product=$subject->getProduct();

        if ($product !== null) {
            $productStock = $this->label->getStockItem($product);
            $selectedLabel = $product->getResource()->getAttributeRawValue(
                $product->getId(),
                'select_label',
                $this->helper->getStoreId()
            );
            $pos = strpos($this->helper->isDisplayOn(), "productpage");
            if (($this->helper->isEnable() == true) && (is_int($pos))) {
                if ($selectedLabel == "new") {
                    return $result . $this->label->getNewProductLabel($product, "newproduct-label");
                }

                //selected sale product label
                if ($selectedLabel == "sale") {
                    return $result . $this->label->getSaleProductLabel($product, "saleproduct-label");
                }
                //selected use general config
                if (($selectedLabel == "none")) {
                    return $result . $this->label->getOutOfStockLabel($productStock, "outofstock-label");
                }
            }
        }
        return $result;
    }
}