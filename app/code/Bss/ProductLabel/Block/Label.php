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
namespace Bss\ProductLabel\Block;

use Magento\Framework\View\Element\Template;
use Magento\CatalogInventory\Api\StockRegistryInterface;

class Label extends Template
{
    /**
     * @var \Bss\ProductLabel\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * Label constructor.
     * @param Template\Context $context
     * @param \Bss\ProductLabel\Helper\Data $helper
     * @param \Magento\Framework\Registry $registry
     * @param StockRegistryInterface $stockRegistry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Bss\ProductLabel\Helper\Data $helper,
        \Magento\Framework\Registry $registry,
        StockRegistryInterface $stockRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper=$helper;
        $this->stockRegistry = $stockRegistry;
        $this->registry = $registry;
        $this->layout = $context->getLayout();
    }

    /**
     * @return mixed
     */
    public function getProductListBlock() {
        $html = $this->layout
            ->createBlock('Bss\ProductLabel\Block\Label')
            ->setTemplate('Bss_ProductLabel::productlist.phtml')
            ->toHtml();
        return $html;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface
     */
    public function getStockItem($product)
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
        return $stockItem;

    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct(){
        return $this->registry->registry('current_label_product');
    }

    /**
     * @param StockRegistryInterface $productStock
     * @param string $class
     * @return string
     */
    public function getOutOfStockLabel($productStock,$class)
    {
        if ($this->helper->isEnableOutOfStockLabel()) {
            $imgName = $this->helper->getOutOfStockImageName();
            $src = $this->helper->resize($imgName);
            if ($productStock->getIsInStock() == 0) {
                return "<div class='list-mode'><img class='$class' src='$src'></div>";
            }
        }
        return "";
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param string $class
     * @return string
     */
    public function getNewProductLabel($product, $class)
    {
        if ($this->helper->isEnableNewProductLabel()) {
            $imgName = $this->helper->getNewProductImageName();
            $src = $this->helper->resize($imgName);
            return "<div class='list-mode'><img class='$class' src='$src'></div>";
        }
        return "";
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param string $class
     * @return string
     */
    public function getSaleProductLabel($product, $class)
    {
        if ($this->helper->isEnableSaleProductLabel()) {
            $imgName = $this->helper->getSaleProductImageName();
            $src = $this->helper->resize($imgName);
            return "<div class='list-mode'><img class='$class' src='$src'></div>";
        }
        return "";
    }

    /**
     * @return string
     */
    public function getOutOfStockPosition()
    {
        return $this->helper->getOutOfStockPosition();
    }

    /**
     * @return string
     */
    public function getNewProductPosition()
    {
        return $this->helper->getNewProductPosition();
    }

    /**
     * @return string
     */
    public function getSaleProductPosition()
    {
        return $this->helper->getSaleProductPosition();
    }
}