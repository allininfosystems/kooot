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

class ProductListPlugin
{
    /**
     * @var Label
     */
    protected $label;

    /**
     * @var \Bss\ProductLabel\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * ProductListPlugin constructor.
     * @param Label $label
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Bss\ProductLabel\Helper\Data $helper
     */
    public function __construct(
        Label $label,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Bss\ProductLabel\Helper\Data $helper
    ) {
        $this->label=$label;
        $this->productFactory = $productFactory;
        $this->helper=$helper;
    }

    /**
     * @param \Magento\Catalog\Block\Product\Image $subject
     * @param $result
     * @return string
     */
    public function afterToHtml(\Magento\Catalog\Block\Product\Image $subject, $result)
    {
        $result.=$this->label->getProductListBlock();
        $product = $this->label->getProduct();

        if ($product !== null) {

            $productStock = $this->label->getStockItem($product);
            $selectedLabel = $product->getResource()->getAttributeRawValue(
                $product->getId(),
                'select_label',
                $this->helper->getStoreId()
            );
            $pos = strpos($this->helper->isDisplayOn(), "productlist");
            if (($this->helper->isEnable() == true) && (is_int($pos))) {
                //selected new product label
                if ($selectedLabel == "new") {
                    return $result . $this->label->getNewProductLabel($product, "newproduct-list");
                }

                //selected sale product label
                if ($selectedLabel == "sale") {
                    return $result . $this->label->getSaleProductLabel($product, "saleproduct-list");
                }
                //selected use general config
                if (($selectedLabel == "none")) {
                    return $result . $this->label->getOutOfStockLabel($productStock, "outofstock-list");
                }
            }
        }
        return $result;
    }
}