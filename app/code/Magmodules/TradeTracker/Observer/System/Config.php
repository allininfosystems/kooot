<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\TradeTracker\Observer\System;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magmodules\TradeTracker\Helper\Config as ConfigHelper;

/**
 * Class Config
 *
 * @package Magmodules\TradeTracker\Observer\System
 */
class Config implements ObserverInterface
{

    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * Config constructor.
     *
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        ConfigHelper $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $section = $observer->getRequest()->getParam('section');
        if ($section == 'magmodules_tradetracker') {
            $this->configHelper->run();
        }
    }
}
