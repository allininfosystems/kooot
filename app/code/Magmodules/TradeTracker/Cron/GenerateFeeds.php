<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\TradeTracker\Cron;

use Magmodules\TradeTracker\Model\Feed as FeedModel;
use Magmodules\TradeTracker\Helper\General as GeneralHelper;

/**
 * Class GenerateFeeds
 *
 * @package Magmodules\TradeTracker\Cron
 */
class GenerateFeeds
{

    /**
     * @var FeedModel
     */
    private $feedModel;
    /**
     * @var GeneralHelper
     */
    private $generalHelper;

    /**
     * GenerateFeeds constructor.
     *
     * @param FeedModel     $feedModel
     * @param GeneralHelper $generalHelper
     */
    public function __construct(
        FeedModel $feedModel,
        GeneralHelper $generalHelper
    ) {
        $this->feedModel = $feedModel;
        $this->generalHelper = $generalHelper;
    }

    /**
     * Execute: Run all TradeTracker Feed generation.
     */
    public function execute()
    {
        try {
            $cronEnabled = $this->generalHelper->getCronEnabled();
            if ($cronEnabled) {
                $this->feedModel->generateAll();
            }
        } catch (\Exception $e) {
            $this->generalHelper->addTolog('Cron', $e->getMessage());
        }
    }
}
