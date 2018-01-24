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
namespace Magenest\ProductLabel\Helper;

use Magenest\ProductLabel\Model\ResourceModel\Label\Collection as LabelCollection;
use Magento\CatalogRule\Model\Rule as RuleModel;
use Magenest\ProductLabel\Helper\Data as DataHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class ImageBuilder
 *
 * @package Magenest\ProductLabel\Helper\Product
 */
class Label
{
    /**
     * @var RuleModel
     */
    protected $_catalogRule;

    /**
     * @var LabelCollection
     */
    protected $_labelCollection;

    /**
     * @var DataHelper
     */
    protected $_dataHelper;

    /**
     * Core config data
     *
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var DateTime
     */
    protected $_coreDate;

    /**
     * @param RuleModel $catalogRule
     * @param LabelCollection $labelCollection
     * @param Data $dataHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param DateTime $coreDate
     */
    public function __construct(
        RuleModel $catalogRule,
        LabelCollection $labelCollection,
        DataHelper $dataHelper,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        DateTime $coreDate
    ) {
        $this->_catalogRule = $catalogRule;
        $this->_labelCollection = $labelCollection;
        $this->_dataHelper = $dataHelper;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager =$storeManager;
        $this->_coreDate = $coreDate;
        $this->_filesystem = $filesystem;
    }

    /**
     * Set Product
     *
     * @param  Product $product
     * @return $this
     */
    public function setProduct(Product $product)
    {
        $this->_product = $product;

        return $this;
    }
    /**
     * Prepare Label Collection
     *
     * @return $this
     */
    protected function getCollection()
    {
        $datetime = $this->_coreDate->gmtDate();

        $startFilter = [
            'or' => [
                0 => ['date' => true, 'to' => $datetime],
                1 => ['is' => new \Zend_Db_Expr('null')],
            ]
        ];
        $endFilter = [
            'or' => [
                0 => ['date' => true, 'from' => $datetime],
                1 => ['is' => new \Zend_Db_Expr('null')],
            ]
        ];
        $collections = $this->_labelCollection->addActiveFilter()
            ->addFieldToFilter('from_date', $startFilter)
            ->addFieldToFilter('to_date', $endFilter)
            ->setOrder('priority', 'ASC')
            ->setOrder('id', 'DESC');

        return $collections;
    }

    /**
     * is New Product
     *
     * @return bool
     */
    public function isNew()
    {
        $days = (int)$this->_scopeConfig->getValue('product_labels/category_new/days');
        $datetime = $this->_coreDate->gmtDate();
        $data = $this->_product->getCreatedAt();

        if ($data) {
            $isNew = (int)strtotime($data) + $days * 86400 - strtotime($datetime);
            if ($isNew > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sales Product
     *
     * @return bool
     */
    public function isSale()
    {
        $isSale = false;
        $price = $this->_catalogRule->calcProductPriceRule($this->_product, $this->_product->getPrice());
        if ($price) {
            $isSale = true;
        }
        $finalFinal = $this->_product->getFinalPrice();
        $price = $this->_product->getPrice();
        if ($price > $finalFinal) {
            $isSale = true;
        }

        return $isSale;
    }

    /**
     * Get Rules of Label
     *
     * @return mixed
     */
    public function getRulesLabel()
    {
        $collections = $this->getCollection();
        $rule = null;
        $id = $this->_product->getId();

        /** @var \Magenest\ProductLabel\Model\Label $collection */
        foreach ($collections as $collection) {
            if ($this->_dataHelper->useLabel($collection, $id)) {
                $rule = $collection;
                $imageCatUrl = $this->getLabelImageUrl($collection->getCategoryImage());
                $rule->setCategoryImageUrl($imageCatUrl);
                $imageProUrl = $this->getLabelImageUrl($collection->getProductImage());
                $imgSize = $this->getLabelImageSize($collection->getCategoryImage());
                $rule->setImageSize($imgSize);
                $rule->setProductImageUrl($imageProUrl);

                break;
            }
        }

        return $rule;
    }

    /**
     * @return DataObject
     */
    public function getDefaultCategorySale()
    {
        $display = $this->_scopeConfig->getValue('product_labels/category_onsale/display');
        $_label = new DataObject;
        if (!$display) {
            return $_label;
        }

        $path = "productlabel/config/sale/".$this->_scopeConfig->getValue('product_labels/category_onsale/image');
        $categoryText = $this->_scopeConfig->getValue('product_labels/category_onsale/text');
        $position = $this->_scopeConfig->getValue('product_labels/category_onsale/position');
        $data = [
            'category_image_url' => $this->getLabelImageUrl($path),
            'category_text' => $categoryText,
            'category_position' => $position,
        ];

        $_label->setData($data);

        return $_label;
    }

    /**
     * Get default label from Configuration
     *
     * @return DataObject
     */
    public function getDefaultProductSale()
    {
        $display = $this->_scopeConfig->getValue('product_labels/product_onsale/display');
        $_label = new DataObject;

        if (!$display) {
            return $_label;
        }

        $path = "productlabel/config/sale/".$this->_scopeConfig->getValue('product_labels/product_onsale/image');
        $categoryText = $this->_scopeConfig->getValue('product_labels/product_onsale/text');
        $position = $this->_scopeConfig->getValue('product_labels/product_onsale/position');
        $data = [
            'product_image_url' => $this->getLabelImageUrl($path),
            'product_text' => $categoryText,
            'product_position' => $position,
        ];

        $_label->setData($data);

        return $_label;
    }

    /**
     * Get label from Configuration
     *
     * @return DataObject|false
     */
    public function getDefaultCategoryNew()
    {
        $display = $this->_scopeConfig->getValue('product_labels/category_new/display');
        $_label = new DataObject;
        if (!$display) {
            return false;
        }

        $path = "productlabel/config/new/".$this->_scopeConfig->getValue('product_labels/category_new/image');
        $categoryText = $this->_scopeConfig->getValue('product_labels/category_new/text');
        $position = $this->_scopeConfig->getValue('product_labels/category_new/position');
        $data = [
            'category_image_url' => $this->getLabelImageUrl($path),
            'category_text' => $categoryText,
            'category_position' => $position
        ];

        $_label->setData($data);

        return $_label;
    }

    /**
     * Get label from Configuration
     *
     * @return DataObject
     */
    public function getDefaultProductNew()
    {
        $display = $this->_scopeConfig->getValue('product_labels/product_new/display');
        $_label = new DataObject;
        if (!$display) {
            return $_label;
        }

        $path = "productlabel/config/new/".$this->_scopeConfig->getValue('product_labels/product_new/image');
        $categoryText = $this->_scopeConfig->getValue('product_labels/product_new/text');
        $position = $this->_scopeConfig->getValue('product_labels/product_new/position');

        $data = [
            'product_image_url' => $this->getLabelImageUrl($path),
            'product_text' => $categoryText,
            'product_position' => $position
        ];

        $_label->setData($data);

        return $_label;
    }

    /**
     * @param  string $path
     * @return string
     */
    public function getLabelImageUrl($path)
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $url = $mediaUrl.$path;
        return $url;
    }

    /**
     * @param  string $imgPath
     * @return string
     */
    protected function getLabelImageSize($imgPath)
    {
        $path = $this->_filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath($imgPath);
        $imageInfo = getimagesize($path);
        if (!isset($imageInfo[0]) || !isset($imageInfo[1])) {
            return $imageInfo = [200, 300];
        }
        return $imageInfo;
    }
}
