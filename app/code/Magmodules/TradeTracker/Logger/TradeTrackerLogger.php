<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\TradeTracker\Logger;

use Monolog\Logger;

/**
 * Class TradeTrackerLogger
 *
 * @package Magmodules\TradeTracker\Logger
 */
class TradeTrackerLogger extends Logger
{

    /**
     * @param $type
     * @param $data
     */
    public function add($type, $data)
    {
        if (is_array($data)) {
            $this->addInfo($type . ': ' . json_encode($data));
        } elseif (is_object($data)) {
            $this->addInfo($type . ': ' . json_encode($data));
        } else {
            $this->addInfo($type . ': ' . $data);
        }
    }
}
