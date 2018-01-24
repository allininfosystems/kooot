<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magmodules\GoogleShopping\Helper\General as GeneralHelper;
use Magmodules\GoogleShopping\Helper\Product as ProductHelper;
use Magmodules\GoogleShopping\Helper\Category as CategoryHelper;
use Magmodules\GoogleShopping\Helper\Feed as FeedHelper;

class Source extends AbstractHelper
{

    const XML_PATH_ID_SOURCE = 'magmodules_googleshopping/data/id_attribute';
    const XML_PATH_NAME_SOURCE = 'magmodules_googleshopping/data/name_attribute';
    const XML_PATH_DESCRIPTION_SOURCE = 'magmodules_googleshopping/data/description_attribute';
    const XML_PATH_IMAGE_SOURCE = 'magmodules_googleshopping/data/image';
    const XML_PATH_CONDITION_TYPE = 'magmodules_googleshopping/data/condition_type';
    const XML_PATH_CONDITION_DEFAULT = 'magmodules_googleshopping/data/condition_default';
    const XML_PATH_CONDITION_SOURCE = 'magmodules_googleshopping/data/condition_attribute';
    const XML_PATH_GTIN_SOURCE = 'magmodules_googleshopping/data/gtin_attribute';
    const XML_PATH_BRAND_SOURCE = 'magmodules_googleshopping/data/brand_attribute';
    const XML_PATH_MPN_SOURCE = 'magmodules_googleshopping/data/mpn_attribute';
    const XML_PATH_COLOR_SOURCE = 'magmodules_googleshopping/data/color_attribute';
    const XML_PATH_MATERIAL_SOURCE = 'magmodules_googleshopping/data/material_attribute';
    const XML_PATH_PATTERN_SOURCE = 'magmodules_googleshopping/data/pattern_attribute';
    const XML_PATH_SIZE_SOURCE = 'magmodules_googleshopping/data/size_attribute';
    const XML_PATH_SIZETYPE_SOURCE = 'magmodules_googleshopping/data/sizetype_attribute';
    const XML_PATH_SIZESYTEM_SOURCE = 'magmodules_googleshopping/data/sizesystem_attribute';
    const XML_PATH_GENDER_SOURCE = 'magmodules_googleshopping/data/gender_attribute';
    const XML_PATH_EXTRA_FIELDS = 'magmodules_googleshopping/advanced/extra_fields';
    const XML_PATH_URL_UTM = 'magmodules_googleshopping/advanced/url_utm';
    const XML_PATH_SHIPPING = 'magmodules_googleshopping/advanced/shipping';
    const XML_PATH_LIMIT = 'magmodules_googleshopping/generate/limit';
    const XML_PATH_WEIGHT_UNIT = 'general/locale/weight_unit';
    const XML_PATH_CATEGORY = 'magmodules_googleshopping/data/category';
    const XML_PATH_VISBILITY = 'magmodules_googleshopping/filter/visbility_enabled';
    const XML_PATH_VISIBILITY_OPTIONS = 'magmodules_googleshopping/filter/visbility';
    const XML_PATH_CATEGORY_FILTER = 'magmodules_googleshopping/filter/category_enabled';
    const XML_PATH_CATEGORY_FILTER_TYPE = 'magmodules_googleshopping/filter/category_type';
    const XML_PATH_CATEGORY_IDS = 'magmodules_googleshopping/filter/category';
    const XML_PATH_STOCK = 'magmodules_googleshopping/filter/stock';
    const XML_PATH_RELATIONS_ENABLED = 'magmodules_googleshopping/advanced/relations';
    const XML_PATH_PARENT_ATTS = 'magmodules_googleshopping/advanced/parent_atts';

    private $generalHelper;
    private $productHelper;
    private $categoryHelper;
    private $feedHelper;
    private $storeManager;

    /**
     * Source constructor.
     *
     * @param Context               $context
     * @param StoreManagerInterface $storeManager
     * @param General               $generalHelper
     * @param Category              $categoryHelper
     * @param Product               $productHelper
     * @param Feed                  $feedHelper
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        GeneralHelper $generalHelper,
        CategoryHelper $categoryHelper,
        ProductHelper $productHelper,
        FeedHelper $feedHelper
    ) {
        $this->generalHelper = $generalHelper;
        $this->productHelper = $productHelper;
        $this->categoryHelper = $categoryHelper;
        $this->feedHelper = $feedHelper;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     *
     * @param $storeId
     * @param $type
     *
     * @return array
     */
    public function getConfig($storeId, $type)
    {
        $config = [];
        $config['type'] = $type;
        $config['store_id'] = $storeId;
        $config['flat'] = false;
        $config['attributes'] = $this->getAttributes();
        $config['price_config'] = $this->getPriceConfig();
        $config['url_type_media'] = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $config['base_url'] = $this->storeManager->getStore()->getBaseUrl();
        $config['feed_locations'] = $this->feedHelper->getFeedLocation($storeId, $type);
        $config['utm_code'] = $this->generalHelper->getStoreValue(self::XML_PATH_URL_UTM);
        $config['filters'] = $this->getProductFilters($type);
        $config['weight_unit'] = $this->generalHelper->getStoreValue(self::XML_PATH_WEIGHT_UNIT);
        $config['default_category'] = $this->generalHelper->getStoreValue(self::XML_PATH_CATEGORY);
        $config['inventory'] = $this->getInventoryData();
        $config['categories'] = $this->categoryHelper->getCollection($storeId, 'googleshopping_cat',
            $config['default_category']);

        return $config;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function getAttributes($type = 'feed')
    {
        $attributes = [];
        $attributes['id'] = [
            'label'                     => 'g:id',
            'source'                    => $this->generalHelper->getStoreValue(self::XML_PATH_ID_SOURCE),
            'max'                       => 50,
            'parent_selection_disabled' => 1
        ];
        $attributes['name'] = [
            'label'  => 'g:title',
            'source' => $this->generalHelper->getStoreValue(self::XML_PATH_NAME_SOURCE),
            'max'    => 150
        ];
        $attributes['description'] = [
            'label'   => 'g:description',
            'source'  => $this->generalHelper->getStoreValue(self::XML_PATH_DESCRIPTION_SOURCE),
            'max'     => 5000,
            'actions' => ['striptags']
        ];
        $attributes['link'] = [
            'label'  => 'g:link',
            'source' => 'product_url',
            'max'    => 2000,
            'parent' => 1
        ];
        $attributes['image_link'] = [
            'label'  => 'g:image_link',
            'source' => $this->generalHelper->getStoreValue(self::XML_PATH_IMAGE_SOURCE),
        ];
        $attributes['price'] = [
            'label'                     => 'g:price',
            'collection'                => 'price',
            'parent_selection_disabled' => 1
        ];
        $attributes['brand'] = [
            'label'  => 'g:brand',
            'source' => $this->generalHelper->getStoreValue(self::XML_PATH_BRAND_SOURCE),
            'max'    => 70
        ];
        $attributes['gtin'] = [
            'label'  => 'g:gtin',
            'source' => $this->generalHelper->getStoreValue(self::XML_PATH_GTIN_SOURCE),
            'max'    => 50
        ];
        $attributes['model'] = [
            'label'  => 'g:mpn',
            'source' => $this->generalHelper->getStoreValue(self::XML_PATH_MPN_SOURCE),
            'max'    => 70
        ];
        $attributes['condition'] = $this->getConditionSource();
        $attributes['color'] = [
            'label'  => 'g:color',
            'source' => $this->generalHelper->getStoreValue(self::XML_PATH_COLOR_SOURCE),
            'max'    => 100
        ];
        $attributes['gender'] = [
            'label'  => 'g:gender',
            'source' => $this->generalHelper->getStoreValue(self::XML_PATH_GENDER_SOURCE)
        ];
        $attributes['material'] = [
            'label'  => 'g:material',
            'source' => $this->generalHelper->getStoreValue(self::XML_PATH_MATERIAL_SOURCE),
            'max'    => 200
        ];
        $attributes['pattern'] = [
            'label'  => 'g:pattern',
            'source' => $this->generalHelper->getStoreValue(self::XML_PATH_PATTERN_SOURCE),
            'max'    => 100
        ];
        $attributes['size'] = [
            'label'  => 'g:size',
            'source' => $this->generalHelper->getStoreValue(self::XML_PATH_SIZE_SOURCE),
            'max'    => 100
        ];
        $attributes['size_type'] = [
            'label'  => 'g:size_type',
            'source' => $this->generalHelper->getStoreValue(self::XML_PATH_SIZETYPE_SOURCE)
        ];
        $attributes['size_system'] = [
            'label'  => 'g:size_system',
            'source' => $this->generalHelper->getStoreValue(self::XML_PATH_SIZESYTEM_SOURCE)
        ];
        $attributes['weight'] = [
            'label'   => 'g:shipping_weight',
            'source'  => 'weight',
            'suffix'  => 'weight_unit',
            'actions' => ['number']
        ];
        $attributes['item_group_id'] = [
            'label'  => 'g:item_group_id',
            'source' => $attributes['id']['source'],
            'parent' => 2
        ];
        $attributes['is_bundle'] = [
            'label'                     => 'g:is_bundle',
            'source'                    => 'type_id',
            'condition'                 => ['bundle:yes'],
            'parent_selection_disabled' => 1,
        ];
        $attributes['availability'] = [
            'label'                     => 'g:availability',
            'source'                    => 'is_in_stock',
            'parent_selection_disabled' => 1,
            'condition'                 => [
                '1:in stock',
                '0:out of stock'
            ]
        ];

        if ($extraFields = $this->getExtraFields()) {
            $attributes = array_merge($attributes, $extraFields);
        }

        if ($type != 'feed') {
            return $attributes;
        } else {
            $parentAttributes = $this->getParentAttributes();
            return $this->productHelper->addAttributeData($attributes, $parentAttributes);
        }
    }

    /**
     * @return array|bool
     */
    public function getConditionSource()
    {
        $conditionType = $this->generalHelper->getStoreValue(self::XML_PATH_CONDITION_TYPE);
        if ($conditionType == 'static') {
            return [
                'label'  => 'g:condition',
                'static' => $this->generalHelper->getStoreValue(self::XML_PATH_CONDITION_DEFAULT)
            ];
        }
        if ($conditionType == 'attribute') {
            return [
                'label'  => 'g:condition',
                'source' => $this->generalHelper->getStoreValue(self::XML_PATH_CONDITION_SOURCE)
            ];
        }

        return false;
    }

    /**
     * @return array
     */
    public function getExtraFields()
    {
        $extraFields = [];
        if ($attributes = $this->generalHelper->getStoreValueArray(self::XML_PATH_EXTRA_FIELDS)) {
            foreach ($attributes as $attribute) {
                $label = strtolower(str_replace(' ', '_', $attribute['name']));
                $extraFields[$label] = [
                    'label'  => $label,
                    'source' => $attribute['attribute']
                ];
            }
        }

        return $extraFields;
    }

    /**
     * @return array|mixed
     */
    public function getParentAttributes()
    {
        $enabled = $this->generalHelper->getStoreValue(self::XML_PATH_RELATIONS_ENABLED);
        if ($enabled) {
            if ($attributes = $this->generalHelper->getStoreValue(self::XML_PATH_PARENT_ATTS)) {
                $attributes = explode(',', $attributes);

                return $attributes;
            }
        }

        return [];
    }

    /**
     * @return array
     */
    public function getPriceConfig()
    {
        $priceFields = [];
        $priceFields['price'] = 'g:price';
        $priceFields['sales_price'] = 'g:sale_price';
        $priceFields['sales_date_range'] = 'g:sale_price_effective_date';
        $priceFields['currency'] = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
        $priceFields['use_currency'] = true;

        return $priceFields;
    }

    /**
     * @param $type
     *
     * @return array
     */
    public function getProductFilters($type)
    {
        $filters = [];

        $visibilityFilter = $this->generalHelper->getStoreValue(self::XML_PATH_VISBILITY);
        if ($visibilityFilter) {
            $visibility = $this->generalHelper->getStoreValue(self::XML_PATH_VISIBILITY_OPTIONS);
            $filters['visibility'] = explode(',', $visibility);
        } else {
            $filters['visibility'] = [
                Visibility::VISIBILITY_IN_CATALOG,
                Visibility::VISIBILITY_IN_SEARCH,
                Visibility::VISIBILITY_BOTH,
            ];
        }

        $relations = $this->generalHelper->getStoreValue(self::XML_PATH_RELATIONS_ENABLED);
        if ($relations) {
            $filters['relations'] = 1;
            if (!$visibilityFilter) {
                array_push($filters['visibility'], Visibility::VISIBILITY_NOT_VISIBLE);
            }
        } else {
            $filters['relations'] = 0;
        }

        if ($type == 'preview') {
            $filters['limit'] = '100';
        } else {
            $filters['limit'] = (int)$this->generalHelper->getStoreValue(self::XML_PATH_LIMIT);
        }

        if ($filters['relations'] == 1) {
            $filters['exclude_parent'] = 1;
        }

        $filters['stock'] = $this->generalHelper->getStoreValue(self::XML_PATH_STOCK);

        $categoryFilter = $this->generalHelper->getStoreValue(self::XML_PATH_CATEGORY_FILTER);
        if ($categoryFilter) {
            $categoryIds = $this->generalHelper->getStoreValue(self::XML_PATH_CATEGORY_IDS);
            $filterType = $this->generalHelper->getStoreValue(self::XML_PATH_CATEGORY_FILTER_TYPE);
            if (!empty($categoryIds) && !empty($filterType)) {
                $filters['category_ids'] = explode(',', $categoryIds);
                $filters['category_type'] = $filterType;
            }
        }

        return $filters;
    }

    /**
     * @return array
     */
    public function getInventoryData()
    {
        $invAtt = [];
        $invAtt['attributes'][] = 'is_in_stock';

        return $invAtt;
    }

    /**
     * @param $dataRow
     * @param $product
     * @param $config
     *
     * @return string
     */
    public function reformatData($dataRow, $product, $config)
    {
        if ($identifierExists = $this->getIdentifierExists($dataRow)) {
            $dataRow = array_merge($dataRow, $identifierExists);
        }
        if (!empty($dataRow['g:image_link'])) {
            if ($imageData = $this->getImageData($dataRow)) {
                $dataRow = array_merge($dataRow, $imageData);
            }
        }
        if ($categoryData = $this->getCategoryData($product, $config['categories'])) {
            $dataRow = array_merge($dataRow, $categoryData);
        }
        if ($shippingPrices = $this->getShippingPrices($dataRow, $config)) {
            $dataRow = array_merge($dataRow, $shippingPrices);
        }
        $xml = $this->getXmlFromArray($dataRow, 'item');

        return $xml;
    }

    /**
     * @param $dataRow
     *
     * @return array
     */
    public function getIdentifierExists($dataRow)
    {
        $identifier = 0;
        $identifierExists = [];

        if (!empty($dataRow['g:gtin'])) {
            $identifier++;
        }
        if (!empty($dataRow['g:brand'])) {
            $identifier++;
        }
        if (!empty($dataRow['g:model'])) {
            $identifier++;
        }
        if ($identifier < 2) {
            $identifierExists['g:identifier_exists'] = 'no';
        }

        return $identifierExists;
    }

    /**
     * @param $product
     * @param $categories
     *
     * @return array
     */
    public function getCategoryData($product, $categories)
    {
        $path = [];
        $level = 0;
        foreach ($product->getCategoryIds() as $catId) {
            if (!empty($categories[$catId])) {
                $category = $categories[$catId];
                if ($category['level'] > $level) {
                    $deepestCategory = $category;
                    $level = $category['level'];
                }
            }
        }
        if (!empty($deepestCategory)) {
            $path['g:product_type'] = implode(' > ', $deepestCategory['path']);
            $path['g:google_product_category'] = $deepestCategory['custom'];
        }

        return $path;
    }

    /**
     * @param $dataRow
     * @param $config
     *
     * @return array
     */
    public function getShippingPrices($dataRow, $config)
    {
        $shippingPrices = [];
        if ($shippingArray = $this->generalHelper->getStoreValueArray(self::XML_PATH_SHIPPING)) {
            $i = 0;
            $currency = $config['price_config']['currency'];
            $price = (!empty($dataRow['g:sales_price']) ? $dataRow['g:sales_price'] : $dataRow['g:price']);
            $price = preg_replace('/([^0-9\.,])/i', '', $price);
            foreach ($shippingArray as $shipping) {
                if (($price >= $shipping['price_from']) && ($price <= $shipping['price_to'])) {
                    if (isset($shipping['code']) && isset($shipping['service'])) {
                        $shippingPrices['g:shipping' . $i] = [
                            'g:country' => $shipping['code'],
                            'g:service' => $shipping['service'],
                            'g:price'   => number_format($shipping['price'], 2, '.', '') . ' ' . $currency
                        ];
                        $i++;
                    }
                }
            }
        }

        return $shippingPrices;
    }

    /**
     * @param $dataRow
     *
     * @return array
     */
    public function getImageData($dataRow)
    {
        $i = 0;
        $imageData = [];

        if (is_array($dataRow['g:image_link'])) {
            $imageLinks = $dataRow['g:image_link'];
            foreach ($imageLinks as $link) {
                if ($i == 0) {
                    $imageData['g:image_link'] = $link;
                } else {
                    $imageData['g:additional_image_link' . $i] = $link;
                }
                $i++;
            }
        } else {
            $imageData['g:image_link'] = $dataRow['image_link'];
        }

        return $imageData;
    }

    /**
     * @param $data
     * @param $type
     *
     * @return string
     */
    public function getXmlFromArray($data, $type)
    {
        $xml = '  <' . $type . '>' . PHP_EOL;
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $key = preg_replace('/[0-9]*$/', '', $key);
                $xml .= '   <' . $key . '>' . PHP_EOL;
                foreach ($value as $key2 => $value2) {
                    if (!empty($value2)) {
                        $xml .= '      <' . $key2 . '>' . htmlspecialchars($value2) . '</' . $key2 . '>' . PHP_EOL;
                    }
                }
                $xml .= '   </' . $key . '>' . PHP_EOL;
            } else {
                if (strpos($key, 'g:additional_image_link') !== false) {
                    $key = 'g:additional_image_link';
                }
                if (!empty($value)) {
                    $xml .= '   <' . $key . '>' . htmlspecialchars($value) . '</' . $key . '>' . PHP_EOL;
                }
            }
        }
        $xml .= '  </' . $type . '>' . PHP_EOL;

        return $xml;
    }
}
