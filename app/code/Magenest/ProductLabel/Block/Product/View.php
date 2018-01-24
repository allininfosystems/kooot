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
namespace Magenest\ProductLabel\Block\Product;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magenest\ProductLabel\Helper\Label as LabelHelper;

/**
 * Label View block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

class View extends AbstractProduct
{
    /**
     * @var LabelHelper
     */
    protected $_labelHelper;

    /**
     * @param Context     $context
     * @param LabelHelper $labelHelper
     * @param array       $data
     */
    public function __construct(
        Context $context,
        LabelHelper $labelHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_labelHelper = $labelHelper;
    }

    /**
     * @return bool
     */
    public function isNewProduct()
    {
        $labelHelper = $this->_labelHelper->setProduct($this->getProduct());

        return $labelHelper->isNew();
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getDefaultNewLabel()
    {
        return $this->_labelHelper->getDefaultProductNew();
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getSaleLabel()
    {
        $labelHelper = $this->_labelHelper->setProduct($this->getProduct());
        $rules = $labelHelper->getRulesLabel();
        if ($rules) {
            $_labelSale = $rules;
        } else {
            $_labelSale = $labelHelper->getDefaultProductSale();
        }

        return $_labelSale;
    }

    /**
     * Check Sale
     *
     * @return bool
     */
    public function isSale()
    {
        $finalPrice = $this->getProduct()->getFinalPrice();
        $price = $this->getProduct()->getPrice();

        return $finalPrice < $price ;
    }
}
