<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\TradeTracker\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magmodules\TradeTracker\Helper\General as GeneralHelper;
use Magmodules\TradeTracker\Helper\Product as ProductHelper;
use Magmodules\TradeTracker\Helper\Category as CategoryHelper;
use Magmodules\TradeTracker\Helper\Feed as FeedHelper;

/**
 * Class Source
 *
 * @package Magmodules\TradeTracker\Helper
 */
class Source extends AbstractHelper
{

    const LIMIT_PREVIEW = 250;
    const XPATH_NAME = 'magmodules_tradetracker/data/name_attribute';
    const XPATH_DESCRIPTION = 'magmodules_tradetracker/data/description_attribute';
    const XPATH_DESCRIPTION_LONG = 'magmodules_tradetracker/data/description_long_attribute';
    const XPATH_IMAGE_SOURCE = 'magmodules_tradetracker/data/image';
    const XPATH_IMAGE_MAIN = 'magmodules_tradetracker/data/main_image';
    const XPATH_EAN = 'magmodules_tradetracker/data/ean_attribute';
    const XPATH_BRAND = 'magmodules_tradetracker/data/brand_attribute';
    const XPATH_COLOR = 'magmodules_tradetracker/data/color_attribute';
    const XPATH_MATERIAL = 'magmodules_tradetracker/data/material_attribute';
    const XPATH_SIZE = 'magmodules_tradetracker/data/size_attribute';
    const XPATH_GENDER = 'magmodules_tradetracker/data/gender_attribute';
    const XPATH_SEND_STOCK = 'magmodules_tradetracker/data/stock';
    const XPATH_EXTRA_INFO = 'magmodules_tradetracker/data/extra_info';
    const XPATH_DELIVERY_TYPE = 'magmodules_tradetracker/data/delivery_type';
    const XPATH_DELIVERY_ATT = 'magmodules_tradetracker/data/delivery_att';
    const XPATH_DELIVERY_IN = 'magmodules_tradetracker/data/delivery_in';
    const XPATH_DELIVERY_OUT = 'magmodules_tradetracker/data/delivery_out';
    const XPATH_CATEGORY_TYPE = 'magmodules_tradetracker/data/category_type';
    const XPATH_CATEGORY_ATTRIBUTE = 'magmodules_tradetracker/data/category_attribute';
    const XPATH_CATEGORY_CUSTOM = 'magmodules_tradetracker/data/category_custom';
    const XPATH_EXTRA_FIELDS = 'magmodules_tradetracker/advanced/extra_fields';
    const XPATH_URL_UTM = 'magmodules_tradetracker/advanced/url_utm';
    const XPATH_SHIPPING = 'magmodules_tradetracker/advanced/shipping';
    const XPATH_WEIGHT_UNIT = 'general/locale/weight_unit';
    const XPATH_VISBILITY = 'magmodules_tradetracker/filter/visbility_enabled';
    const XPATH_VISIBILITY_OPTIONS = 'magmodules_tradetracker/filter/visbility';
    const XPATH_STOCK = 'magmodules_tradetracker/filter/stock';
    const XPATH_RELATIONS_ENABLED = 'magmodules_tradetracker/advanced/relations';
    const XPATH_PARENT_ATTS = 'magmodules_tradetracker/advanced/parent_atts';
    const XPATH_CATEGORY_FILTER = 'magmodules_tradetracker/filter/category_enabled';
    const XPATH_CATEGORY_FILTER_TYPE = 'magmodules_tradetracker/filter/category_type';
    const XPATH_CATEGORY_IDS = 'magmodules_tradetracker/filter/category';
    const XPATH_FILTERS = 'magmodules_tradetracker/filter/filters';
    const XPATH_FILTERS_DATA = 'magmodules_tradetracker/filter/filters_data';
    const XPATH_ADVANCED = 'magmodules_tradetracker/generate/advanced';
    const XPATH_PAGING = 'magmodules_tradetracker/generate/paging';
    const XPATH_DEBUG_MEMORY = 'magmodules_tradetracker/generate/debug_memory';
    const XPATH_LIMIT = 'magmodules_tradetracker/generate/limit';
    const XPATH_CONFIGURABLE = 'magmodules_tradetracker/types/configurable';
    const XPATH_CONFIGURABLE_LINK = 'magmodules_tradetracker/types/configurable_link';
    const XPATH_CONFIGURABLE_IMAGE = 'magmodules_tradetracker/types/configurable_image';
    const XPATH_CONFIGURABLE_PARENT_ATTS = 'magmodules_tradetracker/types/configurable_parent_atts';
    const XPATH_CONFIGURABLE_NONVISIBLE = 'magmodules_tradetracker/types/configurable_nonvisible';
    const XPATH_BUNDLE = 'magmodules_tradetracker/types/bundle';
    const XPATH_BUNDLE_LINK = 'magmodules_tradetracker/types/bundle_link';
    const XPATH_BUNDLE_IMAGE = 'magmodules_tradetracker/types/bundle_image';
    const XPATH_BUNDLE_PARENT_ATTS = 'magmodules_tradetracker/types/bundle_parent_atts';
    const XPATH_BUNDLE_NONVISIBLE = 'magmodules_tradetracker/types/bundle_nonvisible';
    const XPATH_GROUPED = 'magmodules_tradetracker/types/grouped';
    const XPATH_GROUPED_LINK = 'magmodules_tradetracker/types/grouped_link';
    const XPATH_GROUPED_IMAGE = 'magmodules_tradetracker/types/grouped_image';
    const XPATH_GROUPED_PARENT_PRICE = 'magmodules_tradetracker/types/grouped_parent_price';
    const XPATH_GROUPED_PARENT_ATTS = 'magmodules_tradetracker/types/grouped_parrent_atts';
    const XPATH_GROUPED_NONVISIBLE = 'magmodules_tradetracker/types/grouped_nonvisible';

    /**
     * @var General
     */
    private $generalHelper;
    /**
     * @var Product
     */
    private $productHelper;
    /**
     * @var Category
     */
    private $categoryHelper;
    /**
     * @var Feed
     */
    private $feedHelper;
    /**
     * @var StoreManagerInterface
     */
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
        $config['flat'] = false;
        $config['type'] = $type;
        $config['store_id'] = $storeId;
        $config['website_id'] = $this->storeManager->getStore()->getWebsiteId();
        $config['filters'] = $this->getProductFilters($type);
        $config['attributes'] = $this->getAttributes($type, $config['filters']);
        $config['price_config'] = $this->getPriceConfig();
        $config['base_url'] = $this->storeManager->getStore()->getBaseUrl();
        $config['feed_locations'] = $this->feedHelper->getFeedLocation($storeId, $type);
        $config['utm_code'] = $this->generalHelper->getStoreValue(self::XPATH_URL_UTM);
        $config['debug_memory'] = $this->generalHelper->getStoreValue(self::XPATH_DEBUG_MEMORY);
        $config['weight_unit'] = ' ' . $this->generalHelper->getStoreValue(self::XPATH_WEIGHT_UNIT);
        $config['inventory'] = $this->getInventoryData();
        $config['categories'] = $this->categoryHelper->getCollection($storeId, '', '', 'tt_cat_disable_export');

        return $config;
    }

    /**
     * @param $type
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getProductFilters($type)
    {
        $filters = [];
        $filters['type_id'] = ['simple', 'downloadable', 'virtual'];
        $filters['relations'] = [];
        $filters['exclude_parents'] = [];
        $filters['nonvisible'] = [];
        $filters['parent_attributes'] = [];
        $filters['image'] = [];
        $filters['link'] = [];

        $configurabale = $this->generalHelper->getStoreValue(self::XPATH_CONFIGURABLE);
        switch ($configurabale) {
            case "parent":
                array_push($filters['type_id'], 'configurable');
                break;
            case "simple":
                array_push($filters['relations'], 'configurable');
                array_push($filters['exclude_parents'], 'configurable');

                if ($attributes = $this->generalHelper->getStoreValue(self::XPATH_CONFIGURABLE_PARENT_ATTS)) {
                    $filters['parent_attributes']['configurable'] = explode(',', $attributes);
                }

                if ($nonVisible = $this->generalHelper->getStoreValue(self::XPATH_CONFIGURABLE_NONVISIBLE)) {
                    array_push($filters['nonvisible'], 'configurable');
                }

                if ($link = $this->generalHelper->getStoreValue(self::XPATH_CONFIGURABLE_LINK)) {
                    $filters['link']['configurable'] = $link;
                    if (isset($filters['parent_attributes']['configurable'])) {
                        array_push($filters['parent_attributes']['configurable'], 'link');
                    } else {
                        $filters['parent_attributes']['configurable'] = ['link'];
                    }
                }

                if ($image = $this->generalHelper->getStoreValue(self::XPATH_CONFIGURABLE_IMAGE)) {
                    $filters['image']['configurable'] = $image;
                    if (isset($filters['parent_attributes']['configurable'])) {
                        array_push($filters['parent_attributes']['configurable'], 'image_link');
                    } else {
                        $filters['parent_attributes']['configurable'] = ['image_link'];
                    }
                }

                break;
            case "both":
                array_push($filters['type_id'], 'configurable');
                array_push($filters['relations'], 'configurable');

                if ($attributes = $this->generalHelper->getStoreValue(self::XPATH_CONFIGURABLE_PARENT_ATTS)) {
                    $filters['parent_attributes']['configurable'] = explode(',', $attributes);
                }

                if ($nonVisible = $this->generalHelper->getStoreValue(self::XPATH_CONFIGURABLE_NONVISIBLE)) {
                    array_push($filters['nonvisible'], 'configurable');
                }

                if ($link = $this->generalHelper->getStoreValue(self::XPATH_CONFIGURABLE_LINK)) {
                    $filters['link']['configurable'] = $link;
                    if (isset($filters['parent_attributes']['configurable'])) {
                        array_push($filters['parent_attributes']['configurable'], 'link');
                    } else {
                        $filters['parent_attributes']['configurable'] = ['link'];
                    }
                }

                if ($image = $this->generalHelper->getStoreValue(self::XPATH_CONFIGURABLE_IMAGE)) {
                    $filters['image']['configurable'] = $image;
                    if (isset($filters['parent_attributes']['configurable'])) {
                        array_push($filters['parent_attributes']['configurable'], 'image_url');
                    } else {
                        $filters['parent_attributes']['configurable'] = ['image_url'];
                    }
                }

                break;
        }

        $bundle = $this->generalHelper->getStoreValue(self::XPATH_BUNDLE);
        switch ($bundle) {
            case "parent":
                array_push($filters['type_id'], 'bundle');
                break;
            case "simple":
                array_push($filters['relations'], 'bundle');
                array_push($filters['exclude_parents'], 'bundle');

                if ($attributes = $this->generalHelper->getStoreValue(self::XPATH_BUNDLE_PARENT_ATTS)) {
                    $filters['parent_attributes']['bundle'] = explode(',', $attributes);
                }

                if ($nonVisible = $this->generalHelper->getStoreValue(self::XPATH_BUNDLE_NONVISIBLE)) {
                    array_push($filters['nonvisible'], 'bundle');
                }

                if ($link = $this->generalHelper->getStoreValue(self::XPATH_BUNDLE_LINK)) {
                    $filters['link']['bundle'] = $link;
                    if (isset($filters['parent_attributes']['bundle'])) {
                        array_push($filters['parent_attributes']['bundle'], 'link');
                    } else {
                        $filters['parent_attributes']['bundle'] = ['link'];
                    }
                }

                if ($image = $this->generalHelper->getStoreValue(self::XPATH_BUNDLE_IMAGE)) {
                    $filters['image']['bundle'] = $image;
                    if (isset($filters['parent_attributes']['bundle'])) {
                        array_push($filters['parent_attributes']['bundle'], 'image_link');
                    } else {
                        $filters['parent_attributes']['bundle'] = ['image_link'];
                    }
                }

                break;
            case "both":
                array_push($filters['type_id'], 'bundle');
                array_push($filters['relations'], 'bundle');

                if ($attributes = $this->generalHelper->getStoreValue(self::XPATH_BUNDLE_PARENT_ATTS)) {
                    $filters['parent_attributes']['bundle'] = explode(',', $attributes);
                }

                if ($nonVisible = $this->generalHelper->getStoreValue(self::XPATH_BUNDLE_NONVISIBLE)) {
                    array_push($filters['nonvisible'], 'bundle');
                }

                if ($link = $this->generalHelper->getStoreValue(self::XPATH_BUNDLE_LINK)) {
                    $filters['link']['bundle'] = $link;
                    if (isset($filters['parent_attributes']['bundle'])) {
                        array_push($filters['parent_attributes']['bundle'], 'link');
                    } else {
                        $filters['parent_attributes']['bundle'] = ['link'];
                    }
                }

                if ($image = $this->generalHelper->getStoreValue(self::XPATH_BUNDLE_IMAGE)) {
                    $filters['image']['bundle'] = $image;
                    if (isset($filters['parent_attributes']['bundle'])) {
                        array_push($filters['parent_attributes']['bundle'], 'image_link');
                    } else {
                        $filters['parent_attributes']['bundle'] = ['image_link'];
                    }
                }

                break;
        }

        $grouped = $this->generalHelper->getStoreValue(self::XPATH_GROUPED);
        switch ($grouped) {
            case "parent":
                array_push($filters['type_id'], 'grouped');
                break;
            case "simple":
                array_push($filters['relations'], 'grouped');
                array_push($filters['exclude_parents'], 'grouped');

                if ($attributes = $this->generalHelper->getStoreValue(self::XPATH_GROUPED_PARENT_ATTS)) {
                    $filters['parent_attributes']['grouped'] = explode(',', $attributes);
                }

                if ($nonVisible = $this->generalHelper->getStoreValue(self::XPATH_GROUPED_NONVISIBLE)) {
                    array_push($filters['nonvisible'], 'grouped');
                }

                if ($link = $this->generalHelper->getStoreValue(self::XPATH_GROUPED_LINK)) {
                    $filters['link']['grouped'] = $link;
                    if (isset($filters['parent_attributes']['grouped'])) {
                        array_push($filters['parent_attributes']['grouped'], 'link');
                    } else {
                        $filters['parent_attributes']['grouped'] = ['link'];
                    }
                }

                if ($image = $this->generalHelper->getStoreValue(self::XPATH_GROUPED_IMAGE)) {
                    $filters['image']['grouped'] = $image;
                    if (isset($filters['parent_attributes']['grouped'])) {
                        array_push($filters['parent_attributes']['grouped'], 'image_link');
                    } else {
                        $filters['parent_attributes']['grouped'] = ['image_link'];
                    }
                }

                break;
            case "both":
                array_push($filters['type_id'], 'grouped');
                array_push($filters['relations'], 'grouped');

                if ($attributes = $this->generalHelper->getStoreValue(self::XPATH_GROUPED_PARENT_ATTS)) {
                    $filters['parent_attributes']['grouped'] = explode(',', $attributes);
                }

                if ($nonVisible = $this->generalHelper->getStoreValue(self::XPATH_GROUPED_NONVISIBLE)) {
                    array_push($filters['nonvisible'], 'grouped');
                }

                if ($link = $this->generalHelper->getStoreValue(self::XPATH_GROUPED_LINK)) {
                    $filters['link']['grouped'] = $link;
                    if (isset($filters['parent_attributes']['grouped'])) {
                        array_push($filters['parent_attributes']['grouped'], 'link');
                    } else {
                        $filters['parent_attributes']['grouped'] = ['link'];
                    }
                }

                if ($image = $this->generalHelper->getStoreValue(self::XPATH_GROUPED_IMAGE)) {
                    $filters['image']['grouped'] = $image;
                    if (isset($filters['parent_attributes']['grouped'])) {
                        array_push($filters['parent_attributes']['grouped'], 'image_link');
                    } else {
                        $filters['parent_attributes']['grouped'] = ['image_link'];
                    }
                }

                break;
        }

        $visibilityFilter = $this->generalHelper->getStoreValue(self::XPATH_VISBILITY);
        if ($visibilityFilter) {
            $visibility = $this->generalHelper->getStoreValue(self::XPATH_VISIBILITY_OPTIONS);
            $filters['visibility'] = explode(',', $visibility);
            $filters['visibility_parents'] = $filters['visibility'];
        } else {
            $filters['visibility'] = [
                Visibility::VISIBILITY_IN_CATALOG,
                Visibility::VISIBILITY_IN_SEARCH,
                Visibility::VISIBILITY_BOTH,
            ];
            $filters['visibility_parents'] = $filters['visibility'];
            if (!empty($filters['relations'])) {
                array_push($filters['visibility'], Visibility::VISIBILITY_NOT_VISIBLE);
            }
        }

        $filters['limit'] = '';
        if ($type == 'preview') {
            $filters['limit'] = self::LIMIT_PREVIEW;
        } else {
            $advanced = (int)$this->generalHelper->getStoreValue(self::XPATH_ADVANCED);
            $paging = $this->generalHelper->getStoreValue(self::XPATH_PAGING);
            if ($advanced && ($paging > 0)) {
                $filters['limit'] = $paging;
            }
        }

        $filters['stock'] = $this->generalHelper->getStoreValue(self::XPATH_STOCK);

        $categoryFilter = $this->generalHelper->getStoreValue(self::XPATH_CATEGORY_FILTER);
        if ($categoryFilter) {
            $categoryIds = $this->generalHelper->getStoreValue(self::XPATH_CATEGORY_IDS);
            $filterType = $this->generalHelper->getStoreValue(self::XPATH_CATEGORY_FILTER_TYPE);
            if (!empty($categoryIds) && !empty($filterType)) {
                $filters['category_ids'] = explode(',', $categoryIds);
                $filters['category_type'] = $filterType;
            }
        }

        $filters['advanced'] = [];
        $productFilters = $this->generalHelper->getStoreValue(self::XPATH_FILTERS);
        if ($productFilters) {
            if ($advFilters = $this->generalHelper->getStoreValueArray(self::XPATH_FILTERS_DATA)) {
                foreach ($advFilters as $advFilter) {
                    array_push($filters['advanced'], $advFilter);
                }
            }
        }

        return $filters;
    }

    /**
     * @param $type
     * @param $filters
     *
     * @return array
     */
    public function getAttributes($type, $filters = [])
    {
        $attributes = [];
        $attributes['id'] = [
            'label'                     => 'ID',
            'source'                    => 'entity_id',
            'parent_selection_disabled' => 1
        ];
        $attributes['name'] = [
            'label'  => 'name',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_NAME),
            'max'    => 150,
            'xpath'  => self::XPATH_NAME
        ];
        $attributes['description'] = [
            'label'   => 'description',
            'source'  => $this->generalHelper->getStoreValue(self::XPATH_DESCRIPTION),
            'max'     => 1000,
            'actions' => ['striptags'],
            'xpath'   => self::XPATH_DESCRIPTION

        ];
        $attributes['description_long'] = [
            'label'   => 'descriptionLong',
            'source'  => $this->generalHelper->getStoreValue(self::XPATH_DESCRIPTION_LONG),
            'actions' => ['striptags'],
            'xpath'   => self::XPATH_DESCRIPTION_LONG
        ];
        $attributes['link'] = [
            'label'  => 'productURL',
            'source' => 'product_url',
            'parent' => 1
        ];
        $attributes['image_link'] = [
            'label'  => 'imageURL',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_IMAGE_SOURCE),
        ];
        $attributes['price'] = [
            'label'                     => 'price',
            'collection'                => 'price',
            'parent_selection_disabled' => 1
        ];
        $attributes['ean'] = [
            'label'  => 'EAN',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_EAN),
            'xpath'  => self::XPATH_EAN

        ];
        $attributes['brand'] = [
            'label'  => 'brand',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_BRAND),
            'xpath'  => self::XPATH_BRAND

        ];
        $attributes['color'] = [
            'label'  => 'color',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_COLOR),
            'xpath'  => self::XPATH_COLOR

        ];
        $attributes['material'] = [
            'label'  => 'material',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_MATERIAL),
            'xpath'  => self::XPATH_MATERIAL

        ];
        $attributes['size'] = [
            'label'  => 'size',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_SIZE),
            'xpath'  => self::XPATH_SIZE

        ];
        $attributes['gender'] = [
            'label'  => 'gender',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_GENDER),
            'xpath'  => self::XPATH_GENDER
        ];

        $attributes['delivery_time'] = $this->getDeliverySource();

        $attributes['qty'] = [
            'label'                     => 'qty',
            'source'                    => 'qty',
            'actions'                   => ['round'],
            'parent_selection_disabled' => 1,
        ];

        if ($extraInfo = $this->generalHelper->getStoreValue(self::XPATH_EXTRA_INFO)) {
            $attributes['extra_info'] = [
                'label'                     => 'extraInfo',
                'static'                    => $extraInfo,
                'parent_selection_disabled' => 1,
            ];
        }
        $attributes['availability'] = [
            'label'                     => 'availability',
            'source'                    => 'is_in_stock',
            'parent_selection_disabled' => 1,
            'condition'                 => [
                '1:in stock',
                '0:out of stock'
            ]
        ];

        $categoryType = $this->generalHelper->getStoreValue(self::XPATH_CATEGORY_TYPE);
        if ($categoryType == 'custom') {
            $attributes['category_path'] = [
                'label'                     => 'categoryPath',
                'static'                    => $this->generalHelper->getStoreValue(self::XPATH_CATEGORY_CUSTOM),
                'parent_selection_disabled' => 1,
            ];
        }
        if ($categoryType == 'attribute') {
            $attributes['category_path'] = [
                'label'                     => 'categoryPath',
                'source'                    => $this->generalHelper->getStoreValue(self::XPATH_CATEGORY_ATTRIBUTE),
                'parent_selection_disabled' => 1,
            ];
        }

        if ($extraFields = $this->getExtraFields()) {
            $attributes = array_merge($attributes, $extraFields);
        }

        if ($type == 'parent') {
            return $attributes;
        } else {
            $attributes = $this->addAttributeActions($attributes);
            return $this->productHelper->addAttributeData($attributes, $filters);
        }
    }

    /**
     * @return array|bool
     */
    public function getDeliverySource()
    {
        $type = $this->generalHelper->getStoreValue(self::XPATH_DELIVERY_TYPE);
        if ($type == 'static') {
            return [
                'label'     => 'deliveryTime',
                'source'    => 'is_in_stock',
                'condition' => [
                    '1:' . $this->generalHelper->getStoreValue(self::XPATH_DELIVERY_IN),
                    '0:' . $this->generalHelper->getStoreValue(self::XPATH_DELIVERY_OUT),
                ]

            ];
        }
        if ($type == 'attribute') {
            return [
                'label'  => 'deliveryTime',
                'source' => $this->generalHelper->getStoreValue(self::XPATH_DELIVERY_ATT)
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
        if ($attributes = $this->generalHelper->getStoreValueArray(self::XPATH_EXTRA_FIELDS)) {
            $i = 0;
            foreach ($attributes as $attribute) {
                $label = strtolower(str_replace(' ', '_', $attribute['name']));
                $extraFields['extra_' . $i] = [
                    'label'  => $label,
                    'source' => $attribute['attribute']
                ];
                $i++;
            }
        }

        return $extraFields;
    }

    /**
     * @param $attributes
     *
     * @return mixed
     */
    public function addAttributeActions($attributes)
    {
        foreach ($attributes as $key => $attribute) {
            if (!isset($attribute['source']) || !isset($attribute['xpath'])) {
                continue;
            }
            if ($attribute['source'] == 'mm-actions-conditional') {
                if ($condition = $this->parseConditionalField($attribute['xpath'])) {
                    $attributes[$key] = array_merge($attributes[$key], $condition);
                }
            }
            if ($attribute['source'] == 'mm-actions-multi') {
                if ($multi = $this->parseMultiField($attribute['xpath'])) {
                    $attributes[$key] = array_merge($attributes[$key], $multi);
                }
            }
        }
        return $attributes;
    }

    /**
     * @param $xpath
     *
     * @return mixed
     */
    public function parseConditionalField($xpath)
    {
        $xpath = str_replace('_attribute', '_conditional', $xpath);
        $condition = $this->generalHelper->getStoreValue($xpath);

        if (!$condition) {
            return false;
        }

        $condSplit = preg_split("/[?:]+/", str_replace(['(', ')'], '', $condition));
        if (count($condSplit) == 3) {
            preg_match_all("/{{([^}]*)}}/", $condition, $foundAtts);
            return [
                'conditional' => [
                    '*:' . trim($condSplit[2]),
                    trim($condSplit[0]) . ':' . trim($condSplit[1]),
                ],
                'multi'       => implode(',', array_unique($foundAtts[1]))
            ];
        }
    }

    /**
     * @param $xpath
     *
     * @return array|bool
     */
    public function parseMultiField($xpath)
    {
        $xpath = str_replace('_attribute', '_multi', $xpath);
        $multi = $this->generalHelper->getStoreValue($xpath);

        if (!$multi) {
            return false;
        }

        return ['multi' => $multi];
    }

    /**
     * @return array
     */
    public function getPriceConfig()
    {
        $store = $this->storeManager->getStore();

        $priceFields = [];
        $priceFields['price'] = 'fromPrice';
        $priceFields['final_price'] = 'price';
        $priceFields['sales_price'] = 'price';
        $priceFields['discount_perc'] = 'discount';
        $priceFields['currency'] = $store->getCurrentCurrency()->getCode();
        $priceFields['use_currency'] = false;
        $priceFields['exchange_rate'] = $store->getBaseCurrency()->getRate($priceFields['currency']);
        $priceFields['grouped_price_type'] = $this->generalHelper->getStoreValue(self::XPATH_GROUPED_PARENT_PRICE);

        return $priceFields;
    }

    /**
     * @return array
     */
    public function getInventoryData()
    {
        $invAtt = [];
        $invAtt['attributes'][] = 'is_in_stock';

        if ($this->generalHelper->getStoreValue(self::XPATH_SEND_STOCK)) {
            $invAtt['attributes'][] = 'qty';
        }

        return $invAtt;
    }

    /**
     * @param                                $dataRow
     * @param \Magento\Catalog\Model\Product $product
     * @param                                $config
     *
     * @return string
     */
    public function reformatData($dataRow, $product, $config)
    {
        if (empty($dataRow['price']) && !empty($dataRow['fromPrice'])) {
            $dataRow['price'] = $dataRow['fromPrice'];
        }

        if ($shippingPrices = $this->getShippingPrices($dataRow, $config)) {
            $dataRow = array_merge($dataRow, $shippingPrices);
        }
        if ($categoryData = $this->getCategoryData($product, $config['categories'], $dataRow)) {
            $dataRow = array_merge($dataRow, $categoryData);
        }
        if (!empty($dataRow['imageURL'])) {
            if ($imageData = $this->getImageData($dataRow)) {
                $dataRow = array_merge($dataRow, $imageData);
            }
        }

        $xml = $this->getXmlFromArray($dataRow, 'item');

        return $xml;
    }

    /**
     * @param $dataRow
     *
     * @return array
     */
    public function getImageData($dataRow)
    {
        $imageData = [];
        if (is_array($dataRow['imageURL'])) {
            $i = 1;
            $imageLinks = $dataRow['imageURL'];
            foreach ($imageLinks as $link) {
                if (empty($imageData['imageURL'])) {
                    $imageData['imageURL'] = $link;
                } else {
                    $imageData['imageURL' . $i] = $link;
                }
                $i++;
            }
        } else {
            $imageData['imageURL'] = $dataRow['imageURL'];
        }

        return $imageData;
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
        if (!isset($dataRow['price'])) {
            return $shippingPrices;
        }

        if ($shippingArray = $this->generalHelper->getStoreValueArray(self::XPATH_SHIPPING)) {
            $price = (!empty($dataRow['sales_price']) ? $dataRow['sales_price'] : $dataRow['price']);
            $price = preg_replace('/([^0-9\.,])/i', '', $price);
            foreach ($shippingArray as $shipping) {
                if (($price >= $shipping['price_from']) && ($price <= $shipping['price_to'])) {
                    $value = $this->productHelper->formatPrice($shipping['price'], $config['price_config']);
                    $shippingPrices['deliveryCosts'] = $value;
                }
            }
        }

        return $shippingPrices;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param                                $categories
     * @param                                $dataRow
     *
     * @return array
     */
    public function getCategoryData($product, $categories, $dataRow)
    {

        $categoryData = [];
        if (!empty($dataRow['categoryPath'])) {
            $categoryPath = explode(' > ', $dataRow['categoryPath']);
            $title = 'categories';
            foreach ($categoryPath as $path) {
                $categoryData[$title] = $path;
                $title = 'sub' . $title;
            }
            return $categoryData;
        }

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
            $categoryData['categoryPath'] = implode(' > ', $deepestCategory['path']);
            $title = 'categories';
            foreach ($deepestCategory['path'] as $path) {
                $categoryData[$title] = $path;
                $title = 'sub' . $title;
            }
        }

        return $categoryData;
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
                        $xml .= '      <' . $key2 . '>' . htmlspecialchars($value2, ENT_XML1) . '</' . $key2 . '>';
                        $xml .= PHP_EOL;
                    }
                }
                $xml .= '   </' . $key . '>' . PHP_EOL;
            } else {
                if (!empty($value)) {
                    $xml .= '   <' . $key . '>' . htmlspecialchars($value, ENT_XML1) . '</' . $key . '>' . PHP_EOL;
                }
            }
        }
        $xml .= '  </' . $type . '>' . PHP_EOL;

        return $xml;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param                                $type
     *
     * @return string
     */
    public function getProductDataXml($product, $type)
    {
        $productData = [];
        foreach ($product->getData() as $k => $v) {
            if (!is_array($v)) {
                $productData[$k] = $v;
            }
        }

        return $this->getXmlFromArray($productData, $type);
    }
}
