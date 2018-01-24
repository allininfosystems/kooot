<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Model;

use Magmodules\GoogleShopping\Model\Products as ProductModel;
use Magmodules\GoogleShopping\Helper\Source as SourceHelper;
use Magmodules\GoogleShopping\Helper\Product as ProductHelper;
use Magmodules\GoogleShopping\Helper\General as GeneralHelper;
use Magmodules\GoogleShopping\Helper\Feed as FeedHelper;
use Magento\Framework\App\Area;
use Magento\Store\Model\App\Emulation;
use Psr\Log\LoggerInterface;

class Generate
{

    const XML_PATH_FEED_RESULT = 'magmodules_googleshopping/feeds/results';
    const XML_PATH_GENERATE = 'magmodules_googleshopping/generate/enable';

    private $productModel;
    private $sourceHelper;
    private $productHelper;
    private $generalHelper;
    private $feedHelper;

    /**
     * Generate constructor.
     *
     * @param Products        $productModel
     * @param SourceHelper    $sourceHelper
     * @param ProductHelper   $productHelper
     * @param GeneralHelper   $generalHelper
     * @param FeedHelper      $feedHelper
     * @param Emulation       $appEmulation
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductModel $productModel,
        SourceHelper $sourceHelper,
        ProductHelper $productHelper,
        GeneralHelper $generalHelper,
        FeedHelper $feedHelper,
        Emulation $appEmulation,
        LoggerInterface $logger
    ) {
        $this->productModel = $productModel;
        $this->sourceHelper = $sourceHelper;
        $this->productHelper = $productHelper;
        $this->generalHelper = $generalHelper;
        $this->feedHelper = $feedHelper;
        $this->appEmulation = $appEmulation;
        $this->logger = $logger;
    }

    /**
     * Generate all feeds
     * @return array
     */
    public function generateAll()
    {
        $storeFeeds = [];
        $storeIds = $this->generalHelper->getEnabledArray(self::XML_PATH_GENERATE);
        foreach ($storeIds as $storeId) {
            $storeFeeds[] = $this->generateByStore($storeId, 'cron');
        }

        return $storeFeeds;
    }

    /**
     * @param        $storeId
     * @param string $type
     * @param array  $productIds
     * @param int    $page
     *
     * @return array
     */
    public function generateByStore($storeId, $type = 'manual', $productIds = [], $page = 1)
    {
        $timeStart = microtime(true);
        $this->appEmulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);

        $config = $this->sourceHelper->getConfig($storeId, $type);
        $this->feedHelper->createFeed($config);
        $products = $this->productModel->getCollection($config, $productIds, $page);
        $relations = $config['filters']['relations'];
        $limit = $config['filters']['limit'];
        $count = 0;

        foreach ($products as $product) {
            $parent = '';
            if ($relations) {
                if ($parentId = $this->productHelper->getParentId($product->getEntityId())) {
                    $parent = $products->getItemById($parentId);
                    if (!$parent) {
                        $parent = $this->productModel->loadParentProduct($parentId, $config['attributes']);
                    }
                }
            }
            if ($dataRow = $this->productHelper->getDataRow($product, $parent, $config)) {
                if ($row = $this->sourceHelper->reformatData($dataRow, $product, $config)) {
                    $this->feedHelper->writeRow($row);
                    $count++;
                }
            }
        }

        $summary = $this->feedHelper->getFeedSummary($timeStart, $count, $limit);
        $footer = $this->sourceHelper->getXmlFromArray($summary, 'config');
        $this->feedHelper->writeFooter($footer);
        $this->feedHelper->updateResult($storeId, $count, $summary['time'], $summary['date'], $type);

        $this->appEmulation->stopEnvironmentEmulation();

        return [
            'status' => 'success',
            'qty' => $count,
            'path' => $config['feed_locations']['path'],
            'url' => $config['feed_locations']['url']
        ];
    }
}
