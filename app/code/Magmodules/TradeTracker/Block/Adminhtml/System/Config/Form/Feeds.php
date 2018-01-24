<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\TradeTracker\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magmodules\TradeTracker\Helper\Feed as FeedHelper;

/**
 * Class Feeds
 *
 * @package Magmodules\TradeTracker\Block\Adminhtml\System\Config\Form
 */
class Feeds extends Field
{

    /**
     * @var string
     */
    protected $_template = 'Magmodules_TradeTracker::system/config/fieldset/feeds.phtml';
    /**
     * @var FeedHelper
     */
    private $feedHelper;

    /**
     * Feeds constructor.
     *
     * @param Context    $context
     * @param FeedHelper $feedHelper
     */
    public function __construct(
        Context $context,
        FeedHelper $feedHelper
    ) {
        $this->feedHelper = $feedHelper;
        parent::__construct($context);
    }

    /**
     * @return null
     */
    public function getCacheLifetime()
    {
        return null;
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->addClass('magmodules');

        return $this->toHtml();
    }

    /**
     * @return array
     */
    public function getFeedData()
    {
        return $this->feedHelper->getConfigData();
    }

    /**
     * @param $storeId
     *
     * @return string
     */
    public function getDownloadUrl($storeId)
    {
        return $this->getUrl('tradetracker/actions/download/store_id/' . $storeId);
    }

    /**
     * @param $storeId
     *
     * @return string
     */
    public function getGenerateUrl($storeId)
    {
        return $this->getUrl('tradetracker/actions/generate/store_id/' . $storeId);
    }

    /**
     * @param $storeId
     *
     * @return string
     */
    public function getPreviewUrl($storeId)
    {
        return $this->getUrl('tradetracker/actions/preview/store_id/' . $storeId);
    }
}
