<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Cron;

use Magmodules\GoogleShopping\Model\Generate;
use Magmodules\GoogleShopping\Helper\General as GeneralHelper;
use Psr\Log\LoggerInterface;

class GenerateFeeds
{
    private $generate;
    private $generalHelper;
    private $logger;

    /**
     * GenerateFeeds constructor.
     *
     * @param Generate        $generate
     * @param GeneralHelper    $generalHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Generate $generate,
        GeneralHelper $generalHelper,
        LoggerInterface $logger
    ) {
        $this->generate = $generate;
        $this->generalHelper = $generalHelper;
        $this->logger = $logger;
    }

    /**
     * Execute: Run all GoogleShopping Feed generation.
     */
    public function execute()
    {
        try {
            $cronEnabled = $this->generalHelper->getCronEnabled();
            if ($cronEnabled) {
                $this->generate->generateAll();
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }
}
