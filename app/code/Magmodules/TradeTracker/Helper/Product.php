<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\TradeTracker\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\Product\Gallery\ReadHandler as GalleryReadHandler;
use Magento\Catalog\Helper\Image as ProductImageHelper;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Framework\Filter\FilterManager;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable as ConfigurableResource;
use Magento\GroupedProduct\Model\ResourceModel\Product\Link as GroupedResource;
use Magento\Bundle\Model\ResourceModel\Selection as BundleResource;
use Magento\Catalog\Model\Product\CatalogPrice;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\Product\Media\Config as CatalogProductMediaConfig;

/**
 * Class Product
 *
 * @package Magmodules\TradeTracker\Helper
 */
class Product extends AbstractHelper
{

    /**
     * @var EavConfig
     */
    private $eavConfig;
    /**
     * @var FilterManager
     */
    private $filter;
    /**
     * @var ConfigurableResource
     */
    private $catalogProductTypeConfigurable;
    /**
     * @var GroupedResource
     */
    private $catalogProductTypeGrouped;
    /**
     * @var BundleResource
     */
    private $catalogProductTypeBundle;
    /**
     * @var AttributeSetRepositoryInterface
     */
    private $attributeSet;
    /**
     * @var ProductImageHelper
     */
    private $productImageHelper;
    /**
     * @var GalleryReadHandler
     */
    private $galleryReadHandler;
    /**
     * @var CatalogProductMediaConfig
     */
    private $catalogProductMediaConfig;
    /**
     * @var CatalogPrice
     */
    private $commonPriceModel;

    /**
     * Product constructor.
     *
     * @param Context                         $context
     * @param GalleryReadHandler              $galleryReadHandler
     * @param CatalogProductMediaConfig       $catalogProductMediaConfig
     * @param ProductImageHelper              $productImageHelper
     * @param EavConfig                       $eavConfig
     * @param FilterManager                   $filter
     * @param AttributeSetRepositoryInterface $attributeSet
     * @param GroupedResource                 $catalogProductTypeGrouped
     * @param BundleResource                  $catalogProductTypeBundle
     * @param ConfigurableResource            $catalogProductTypeConfigurable
     * @param CatalogPrice                    $commonPriceModel
     */
    public function __construct(
        Context $context,
        GalleryReadHandler $galleryReadHandler,
        CatalogProductMediaConfig $catalogProductMediaConfig,
        ProductImageHelper $productImageHelper,
        EavConfig $eavConfig,
        FilterManager $filter,
        AttributeSetRepositoryInterface $attributeSet,
        GroupedResource $catalogProductTypeGrouped,
        BundleResource $catalogProductTypeBundle,
        ConfigurableResource $catalogProductTypeConfigurable,
        CatalogPrice $commonPriceModel
    ) {
        $this->galleryReadHandler = $galleryReadHandler;
        $this->catalogProductMediaConfig = $catalogProductMediaConfig;
        $this->productImageHelper = $productImageHelper;
        $this->eavConfig = $eavConfig;
        $this->filter = $filter;
        $this->attributeSet = $attributeSet;
        $this->catalogProductTypeConfigurable = $catalogProductTypeConfigurable;
        $this->catalogProductTypeGrouped = $catalogProductTypeGrouped;
        $this->catalogProductTypeBundle = $catalogProductTypeBundle;
        $this->commonPriceModel = $commonPriceModel;
        parent::__construct($context);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Product $parent
     * @param                                $config
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getDataRow($product, $parent, $config)
    {
        $dataRow = [];

        if (!$this->validateProduct($product, $parent, $config)) {
            return $dataRow;
        }

        if ($this->checkBackorder($product, $config['inventory'])) {
            $product->setIsInStock(2);
        }

        foreach ($config['attributes'] as $type => $attribute) {
            $simple = null;
            $productData = $product;
            if ($parent) {
                $parentTypeId = $parent->getTypeId();
                if (isset($attribute['parent'][$parentTypeId])) {
                    if ($attribute['parent'][$parentTypeId] > 0) {
                        $productData = $parent;
                        $simple = $product;
                    }
                }
            }
            if (($attribute['parent']['simple'] == 2) && !$parent) {
                continue;
            }

            if (!empty($attribute['source']) || ($type == 'image_link')) {
                $value = $this->getAttributeValue(
                    $type,
                    $attribute,
                    $config,
                    $productData,
                    $simple
                );
            }

            if (!empty($attribute['multi']) && empty($attribute['conditional']) && empty($attribute['condition'])) {
                foreach ($attribute['multi'] as $multi) {
                    $value = $this->getAttributeValue(
                        $type,
                        $multi,
                        $config,
                        $productData,
                        $simple
                    );
                    if (!empty($value)) {
                        break;
                    }
                }
            }
            if (!empty($attribute['static'])) {
                $value = $attribute['static'];
            }

            if (!empty($attribute['config'])) {
                $value = $config[$attribute['config']];
            }

            if (!empty($attribute['condition'])) {
                $value = $this->getCondition(
                    $attribute['condition'],
                    $productData,
                    $attribute
                );
            }

            if (!empty($attribute['conditional'])) {
                $value = $this->getConditional($attribute, $productData);
            }

            if (!empty($attribute['collection'])) {
                if ($dataCollection = $this->getAttributeCollection($type, $config, $productData)) {
                    $dataRow = array_merge($dataRow, $dataCollection);
                }
            }

            if (!empty($value)) {
                if (!empty($dataRow[$attribute['label']])) {
                    if (is_array($dataRow[$attribute['label']])) {
                        $dataRow[$attribute['label']][] = $value;
                    } else {
                        $data = [$dataRow[$attribute['label']], $value];
                        unset($dataRow[$attribute['label']]);
                        $dataRow[$attribute['label']] = $data;
                    }
                } else {
                    $dataRow[$attribute['label']] = $value;
                }
            }

            $value = null;
        }

        return $dataRow;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Product $parent
     * @param                                $config
     *
     * @return bool
     */
    public function validateProduct($product, $parent, $config)
    {
        $filters = $config['filters'];
        if (!empty($parent)) {
            if (!empty($filters['stock'])) {
                if ($parent->getIsInStock() == 0) {
                    return false;
                }
            }
        }

        if ($product->getVisibility() == Visibility::VISIBILITY_NOT_VISIBLE) {
            if (empty($parent)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param                                $inv
     *
     * @return bool
     */
    public function checkBackorder($product, $inv)
    {

        if ($product->getUseConfigManageStock() && (!$inv['config_manage_stock'])) {
            return false;
        }

        if (!$product->getUseConfigManageStock() && !$product->getManageStock()) {
            return false;
        }

        if (!$product->getIsInStock()) {
            return false;
        }

        if ($product->getIsInStock() && ($product->getQty() > 0)) {
            return false;
        }

        if ($product->getUseConfigBackorders() && empty($inv['config_backorders'])) {
            return false;
        }

        if (!$product->getBackorders()) {
            return false;
        }

        return true;
    }

    /**
     * @param                                $type
     * @param                                $attribute
     * @param                                $config
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Product $simple
     *
     * @return mixed|string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getAttributeValue($type, $attribute, $config, $product, $simple)
    {
        switch ($type) {
            case 'link':
                $value = $this->getProductUrl($product, $simple, $config);
                break;
            case 'image_link':
                $value = $this->getImage($attribute, $config, $product);
                if (($product->getTypeId() == 'configurable') && $simple != null) {
                    if (isset($config['filters']['image']['configurable'])) {
                        if ($config['filters']['image']['configurable'] == 2) {
                            $imageSimple = $this->getImage($attribute, $config, $simple);
                            if (!empty($imageSimple)) {
                                $value = $imageSimple;
                            }
                        }
                    }
                }
                break;
            case 'attribute_set_id':
                $value = $this->getAttributeSetName($product);
                break;
            case 'manage_stock':
            case 'min_sale_qty':
            case 'qty_increments':
            case 'allow_backorder':
                $value = $this->getStockValue($type, $product, $config['inventory']);
                break;
            default:
                $value = $this->getValue($attribute, $product);
                break;
        }

        if (!empty($value)) {
            if (!empty($attribute['actions']) || !empty($attribute['max'])) {
                $value = $this->getFormat($value, $attribute);
            }
            if (!empty($attribute['suffix'])) {
                if (!empty($config[$attribute['suffix']])) {
                    $value .= $config[$attribute['suffix']];
                }
            }
        } else {
            if (!empty($attribute['default'])) {
                $value = $attribute['default'];
            }
        }
        return $value;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Product $simple
     * @param                                $config
     *
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getProductUrl($product, $simple, $config)
    {
        $url = null;
        if ($requestPath = $product->getRequestPath()) {
            $url = $config['base_url'] . $requestPath;
        }
        if (!empty($config['utm_code'])) {
            if ($config['utm_code'][0] != '?') {
                $url .= '?' . $config['utm_code'];
            } else {
                $url .= $config['utm_code'];
            }
        }
        if (!empty($simple)) {
            if ($product->getTypeId() == 'configurable') {
                if (isset($config['filters']['link']['configurable'])) {
                    if ($config['filters']['link']['configurable'] == 2) {
                        $options = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
                        foreach ($options as $option) {
                            if ($id = $simple->getResource()->getAttributeRawValue(
                                $simple->getId(),
                                $option['attribute_code'],
                                $config['store_id']
                            )
                            ) {
                                $urlExtra[] = $option['attribute_id'] . '=' . $id;
                            }
                        }
                    }
                }
            }
            if (!empty($urlExtra) && !empty($url)) {
                $url = $url . '#' . implode('&', $urlExtra);
            }
        }

        return $url;
    }

    /**
     * @param                                $attribute
     * @param                                $config
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getImage($attribute, $config, $product)
    {
        if (empty($attribute['source']) || ($attribute['source'] == 'all')) {
            $images = [];
            if (!empty($attribute['main']) && ($attribute['main'] != 'last')) {
                if ($url = $product->getData($attribute['main'])) {
                    if ($url != 'no_selection') {
                        $images[] = $this->catalogProductMediaConfig->getMediaUrl($url);
                    }
                }
            }
            $this->galleryReadHandler->execute($product);
            $galleryImages = $product->getMediaGallery('images');
            foreach ($galleryImages as $image) {
                if (empty($image['disabled']) || !empty($config['inc_hidden_image'])) {
                    $images[] = $this->catalogProductMediaConfig->getMediaUrl($image['file']);
                }
            }
            if (!empty($attribute['main']) && ($attribute['main'] == 'last')) {
                $imageCount = count($images);
                if ($imageCount > 1) {
                    $mainImage = $images[$imageCount - 1];
                    array_unshift($images, $mainImage);
                }
            }

            return array_unique($images);
        } else {
            $img = null;
            if (!empty($attribute['resize'])) {
                $source = $attribute['source'];
                $size = $attribute['resize'];
                return $this->getResizedImage($product, $source, $size);
            }
            if ($url = $product->getData($attribute['source'])) {
                if ($url != 'no_selection') {
                    $img = $this->catalogProductMediaConfig->getMediaUrl($url);
                }
            }

            if (empty($img)) {
                $source = $attribute['source'];
                if ($source == 'image') {
                    $source = 'small_image';
                }
                if ($url = $product->getData($source)) {
                    if ($url != 'no_selection') {
                        $img = $this->catalogProductMediaConfig->getMediaUrl($url);
                    }
                }
            }
            return $img;
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param                                $source
     * @param                                $size
     *
     * @return string
     */
    public function getResizedImage($product, $source, $size)
    {
        $size = explode('x', $size);
        $width = $size[0];
        $height = end($size);

        $imageId = [
            'image'       => 'product_base_image',
            'thumbnail'   => 'product_thumbnail_image',
            'small_image' => 'product_small_image'
        ];

        if (isset($imageId[$source])) {
            $source = $imageId[$source];
        } else {
            $source = 'product_base_image';
        }

        $resizedImage = $this->productImageHelper->init($product, $source)
            ->constrainOnly(true)
            ->keepAspectRatio(true)
            ->keepTransparency(true)
            ->keepFrame(false);

        if ($height > 0 && $width > 0) {
            $resizedImage->resize($width, $height);
        } else {
            $resizedImage->resize($width);
        }

        return $resizedImage->getUrl();
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return mixed
     */
    public function getAttributeSetName($product)
    {
        $attributeSetRepository = $this->attributeSet->get($product->getAttributeSetId());
        return $attributeSetRepository->getAttributeSetName();
    }

    /**
     * @param                                $attribute
     * @param \Magento\Catalog\Model\Product $product
     * @param                                $inventory
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getStockValue($attribute, $product, $inventory)
    {
        if ($attribute == 'manage_stock') {
            if ($product->getData('use_config_manage_stock')) {
                return $inventory['config_manage_stock'];
            } else {
                return $product->getData('manage_stock');
            }
        }
        if ($attribute == 'min_sale_qty') {
            if ($product->getData('use_config_min_sale_qty')) {
                return $inventory['config_min_sale_qty'];
            } else {
                return $product->getData('min_sale_qty');
            }
        }
        if ($attribute == 'backorders') {
            if ($product->getData('use_config_backorders')) {
                return $inventory['use_config_backorders'];
            } else {
                return $product->getData('backorders');
            }
        }
        if ($attribute == 'qty_increments') {
            if ($product->getData('use_config_enable_qty_inc')) {
                if (!$inventory['config_enable_qty_inc']) {
                    return false;
                }
            } else {
                if (!$product->getData('enable_qty_inc')) {
                    return false;
                }
            }
            if ($product->getData('use_config_qty_increments')) {
                return $inventory['config_qty_increments'];
            } else {
                return $product->getData('qty_increments');
            }
        }

        return '';
    }

    /**
     * @param                                $attribute
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getValue($attribute, $product)
    {
        if ($attribute['type'] == 'media_image') {
            if ($url = $product->getData($attribute['source'])) {
                return $this->catalogProductMediaConfig->getMediaUrl($url);
            }
        }
        if ($attribute['type'] == 'select') {
            if ($attr = $product->getResource()->getAttribute($attribute['source'])) {
                $value = $product->getData($attribute['source']);
                $data = $attr->getSource()->getOptionText($value);
                if (!is_array($data)) {
                    return (string)$data;
                }
            }
        }
        if ($attribute['type'] == 'multiselect') {
            if ($attr = $product->getResource()->getAttribute($attribute['source'])) {
                $value_text = [];
                $values = explode(',', $product->getData($attribute['source']));
                foreach ($values as $value) {
                    $value_text[] = $attr->getSource()->getOptionText($value);
                }
                return implode('/', $value_text);
            }
        }

        return $product->getData($attribute['source']);
    }

    /**
     * @param $value
     * @param $attribute
     *
     * @return mixed|string
     */
    public function getFormat($value, $attribute)
    {
        if (!empty($attribute['actions'])) {
            $breaks = [
                '<br>',
                '<br/>',
                '<br />',
                '</p>',
                '</h1>',
                '</h2>',
                '</h3>',
                '</h4>',
                '</h5>',
                '</h6>',
                '<hr>',
                '</hr>',
                '</li>'
            ];

            $actions = $attribute['actions'];
            if (in_array('striptags', $actions)) {
                $value = str_replace(["\r", "\n"], " ", $value);
                $value = str_replace($breaks, " ", $value);
                $value = str_replace('  ', ' ', $value);
                $value = strip_tags($value);
            }
            if (in_array('number', $actions)) {
                if (is_numeric($value)) {
                    $value = number_format($value, 2);
                }
            }
            if (in_array('round', $actions)) {
                if (is_numeric($value)) {
                    $value = round($value);
                }
            }
            if (in_array('replacetags', $actions)) {
                $value = str_replace(["\r", "\n"], " ", $value);
                $value = str_replace($breaks, " ", '\\' . '\n', $value);
                $value = str_replace('  ', ' ', $value);
                $value = strip_tags($value);
            }
            if (in_array('replacetagsn', $actions)) {
                $value = str_replace(["\r", "\n"], "", $value);
                $value = str_replace("<li>", "- ", $value);
                $value = str_replace($breaks, " ", '\\' . '\n', $value);
                $value = str_replace('  ', ' ', $value);
                $value = strip_tags($value);
            }
        }
        if (!empty($attribute['max'])) {
            $value = $this->filter->truncate($value, ['length' => $attribute['max']]);
        }

        return rtrim($value);
    }

    /**
     * @param                                $conditions
     * @param \Magento\Catalog\Model\Product $product
     * @param                                $attribute
     *
     * @return string
     */
    public function getCondition($conditions, $product, $attribute)
    {
        $data = null;
        $value = $product->getData($attribute['source']);

        foreach ($conditions as $condition) {
            $ex = explode(':', $condition);
            if ($ex['0'] == '*') {
                $data = str_replace($ex[0] . ':', '', $condition);
            }
            if ($value == $ex['0']) {
                $data = str_replace($ex[0] . ':', '', $condition);
            }
        }

        if (!empty($attribute['multi'])) {
            $attributes = $attribute['multi'];
            foreach ($attributes as $att) {
                $data = str_replace('{{' . $att['source'] . '}}', $this->getValue($att, $product), $data);
            }
        }

        return $data;
    }

    /**
     * @param $attribute
     * @param $product
     *
     * @return mixed|string
     */
    public function getConditional($attribute, $product)
    {
        $dataIf = null;
        $dataElse = null;
        $conditions = $attribute['conditional'];
        $attributes = $attribute['multi'];

        $values = [];
        foreach ($attributes as $att) {
            $values['{{' . $att['source'] . '}}'] = $this->getValue($att, $product);
        }

        foreach ($conditions as $condition) {
            $ex = explode(':', $condition);
            if ($ex['0'] == '*') {
                if (isset($ex['1'])) {
                    $dataElse = $ex['1'];
                }
            } else {
                if (!empty($values[$ex['0']]) && isset($ex['1'])) {
                    $dataIf = $ex['1'];
                }
            }
        }

        foreach ($values as $key => $value) {
            $dataIf = str_replace($key, $value, $dataIf);
            $dataElse = str_replace($key, $value, $dataElse);
        }

        if (!empty($attribute['actions'])) {
            $dataIf = $this->getFormat($dataIf, $attribute);
            $dataElse = $this->getFormat($dataElse, $attribute);
        }

        if (!empty($dataIf)) {
            return $dataIf;
        }

        return $dataElse;
    }

    /**
     * @param                                $type
     * @param                                $config
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return array
     */
    public function getAttributeCollection($type, $config, $product)
    {
        if ($type == 'price') {
            return $this->getPriceCollection($config, $product);
        }

        return [];
    }

    /**
     * @param                                $config
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getPriceCollection($config, $product)
    {
        switch ($product->getTypeId()) {
            case 'grouped':
                $groupedPriceType = null;
                if (!empty($config['price_config']['grouped_price_type'])) {
                    $groupedPriceType = $config['price_config']['grouped_price_type'];
                }

                $groupedPrices = $this->getGroupedPrices($product, $config);
                $price = $groupedPrices['min_price'];
                $product['min_price'] = $groupedPrices['min_price'];
                $product['max_price'] = $groupedPrices['max_price'];
                $product['total_price'] = $groupedPrices['total_price'];

                if ($groupedPriceType == 'max') {
                    $price = $groupedPrices['max_price'];
                }

                if ($groupedPriceType == 'total') {
                    $price = $groupedPrices['total_price'];
                }

                break;
            case 'bundle':
                $price = $product->getPrice();
                if (empty($price) && !empty($product['min_price'])) {
                    $price = $product['min_price'];
                }

                if (!empty($product['special_price'])) {
                    $specialPrice = round(($price * $product['special_price'] / 100), 2);
                }

                break;
            default:
                $price = $product->getPrice();
                $finalPrice = $product->getFinalPrice();
                $specialPrice = $product->getSpecialPrice();
        }

        $prices = [];
        $config = $config['price_config'];

        $prices[$config['price']] = $this->formatPrice($price, $config);

        if (isset($finalPrice) && !empty($config['final_price'])) {
            $prices[$config['final_price']] = $this->formatPrice($finalPrice, $config);
        }

        if (isset($finalPrice) && ($price > $finalPrice) && !empty($config['sales_price'])) {
            $prices[$config['sales_price']] = $this->formatPrice($finalPrice, $config);
        }

        if (isset($specialPrice) && ($price > $specialPrice) && !empty($config['sales_price'])) {
            $prices[$config['sales_price']] = $this->formatPrice($specialPrice, $config);
        }

        if (isset($specialPrice) && ($specialPrice < $price) && !empty($config['sales_date_range'])) {
            if ($product->getSpecialFromDate() && $product->getSpecialToDate()) {
                $from = date('Y-m-d', strtotime($product->getSpecialFromDate()));
                $to = date('Y-m-d', strtotime($product->getSpecialToDate()));
                $prices[$config['sales_date_range']] = $from . '/' . $to;
            }
        }

        if ($price <= 0) {
            if (!empty($product['min_price'])) {
                $prices[$config['price']] = $this->formatPrice($product['min_price'], $config);
            }
        }

        if (!empty($product['min_price']) && !empty($config['min_price'])) {
            $prices[$config['min_price']] = $this->formatPrice($product['min_price'], $config);
        }

        if (!empty($product['max_price']) && !empty($config['max_price'])) {
            $prices[$config['max_price']] = $this->formatPrice($product['max_price'], $config);
        }

        if (!empty($product['total_price']) && !empty($config['total_price'])) {
            $prices[$config['total_price']] = $this->formatPrice($product['total_price'], $config);
        }

        if (!empty($config['discount_perc']) && isset($prices[$config['sales_price']])) {
            if ($prices[$config['price']] > 0) {
                $discount = ($prices[$config['sales_price']] - $prices[$config['price']]) / $prices[$config['price']];
                $discount = $discount * -100;
                if ($discount > 0) {
                    $prices[$config['discount_perc']] = round($discount, 1) . '%';
                }
            }
        }

        return $prices;
    }

    /**
     * @param $product
     * @param $config
     *
     * @return array|null
     */
    public function getGroupedPrices($product, $config)
    {
        $subProducts = $product->getTypeInstance()->getAssociatedProducts($product);

        $minPrice = null;
        $maxPrice = null;
        $totalPrice = null;

        foreach ($subProducts as $subProduct) {
            $subProduct->setWebsiteId($config['website_id']);
            if ($subProduct->isSalable()) {
                $price = $this->commonPriceModel->getCatalogPrice($subProduct);
                if ($price < $minPrice || $minPrice === null) {
                    $minPrice = $this->commonPriceModel->getCatalogPrice($subProduct);
                    $product->setTaxClassId($subProduct->getTaxClassId());
                }
                if ($price > $maxPrice || $maxPrice === null) {
                    $maxPrice = $this->commonPriceModel->getCatalogPrice($subProduct);
                    $product->setTaxClassId($subProduct->getTaxClassId());
                }
                $totalPrice += $price;
            }
        }

        return ['min_price' => $minPrice, 'max_price' => $maxPrice, 'total_price' => $totalPrice];
    }

    /**
     * @param $price
     * @param $config
     *
     * @return string
     */
    public function formatPrice($price, $config)
    {
        if (!empty($config['exchange_rate'])) {
            $price = $price * $config['exchange_rate'];
        }

        $decimal = isset($config['decimal_point']) ? $config['decimal_point'] : '.';
        $price = number_format(floatval(str_replace(',', '.', $price)), 2, $decimal, '');
        if (!empty($config['use_currency']) && ($price >= 0)) {
            $price .= ' ' . $config['currency'];
        }
        return $price;
    }

    /**
     * @param        $attributes
     * @param array  $filters
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function addAttributeData($attributes, $filters)
    {
        foreach ($attributes as $key => $value) {
            if (!empty($value['source'])) {
                if (!empty($value['multi'])) {
                    $multipleSources = explode(',', $value['multi']);
                    $sourcesArray = [];
                    foreach ($multipleSources as $source) {
                        if (strlen($source)) {
                            $type = $this->eavConfig->getAttribute('catalog_product', $source)->getFrontendInput();
                            $sourcesArray[] = ['type' => $type, 'source' => $source];
                        }
                    }
                    if (!empty($sourcesArray)) {
                        $attributes[$key]['multi'] = $sourcesArray;
                        if ($attributes[$key]['source'] == 'multi' || $attributes[$key]['source'] == 'conditional') {
                            unset($attributes[$key]['source']);
                        }
                    } else {
                        unset($attributes[$key]);
                    }
                }
                if (!empty($value['source'])) {
                    $type = $this->eavConfig->getAttribute('catalog_product', $value['source'])->getFrontendInput();
                    $attributes[$key]['type'] = $type;
                }
            }

            if (isset($attributes[$key]['parent'])) {
                unset($attributes[$key]['parent']);
            }

            $attributes[$key]['parent']['simple'] = (!empty($value['parent']) ? $value['parent'] : 0);

            if (isset($attributes[$key])) {
                if (isset($filters['parent_attributes'])) {
                    foreach ($filters['parent_attributes'] as $k => $v) {
                        if (in_array($key, $v)) {
                            $attributes[$key]['parent'][$k] = 1;
                        } else {
                            $parent = (!empty($value['parent']) ? $value['parent'] : 0);
                            $attributes[$key]['parent'][$k] = $parent;
                        }
                    }
                }
            }
        }
        return $attributes;
    }

    /**
     * @param $products
     * @param $config
     *
     * @return array
     */
    public function getParentsFromCollection($products, $config)
    {
        $ids = [];
        $filters = $config['filters'];
        if (!empty($filters['relations'])) {
            foreach ($products as $product) {
                if ($parentIds = $this->getParentId($product, $filters)) {
                    $ids[$product->getEntityId()] = $parentIds;
                }
            }
        }
        return $ids;
    }

    /**
     * Return Parent ID from Simple.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param                                $filters
     *
     * @return bool|string
     */
    public function getParentId($product, $filters)
    {
        $productId = $product->getEntityId();
        $visibility = $product->getVisibility();
        $parentIds = [];

        if (!in_array($product->getTypeId(), ['simple', 'downloadable', 'virtual'])) {
            return false;
        }

        if (in_array('configurable', $filters['relations'])
            && (($visibility == Visibility::VISIBILITY_NOT_VISIBLE) || !in_array(
                    'configurable',
                    $filters['nonvisible']
                ))
        ) {
            $configurableIds = $this->catalogProductTypeConfigurable->getParentIdsByChild($productId);
            if (!empty($configurableIds)) {
                $parentIds = array_merge($parentIds, $configurableIds);
            }
        }

        if (in_array('grouped', $filters['relations'])
            && (($visibility == Visibility::VISIBILITY_NOT_VISIBLE) || !in_array('grouped', $filters['nonvisible']))
        ) {
            $typeId = \Magento\GroupedProduct\Model\ResourceModel\Product\Link::LINK_TYPE_GROUPED;
            $groupedIds = $this->catalogProductTypeGrouped->getParentIdsByChild($productId, $typeId);
            if (!empty($groupedIds)) {
                $parentIds = array_merge($parentIds, $groupedIds);
            }
        }

        if (in_array('bundle', $filters['relations'])
            && (($visibility == Visibility::VISIBILITY_NOT_VISIBLE) || !in_array('bundle', $filters['nonvisible']))
        ) {
            $bundleIds = $this->catalogProductTypeBundle->getParentIdsByChild($productId);
            if (!empty($bundleIds)) {
                $parentIds = array_merge($parentIds, $bundleIds);
            }
        }

        return array_unique($parentIds);
    }
}
