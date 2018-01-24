<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\TradeTracker\Block\Adminhtml\Magmodules;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magmodules\TradeTracker\Helper\General as GeneralHelper;
use Magento\Backend\Block\Template\Context;

/**
 * Class Support
 *
 * @package Magmodules\TradeTracker\Block\Adminhtml\Magmodules
 */
class Support extends Field
{

    const MODULE_CODE = 'tradetracker-magento2';
    const SUPPORT_LINK = 'https://www.magmodules.eu/help/' . self::MODULE_CODE;
    const MANUAL_LINK = 'https://www.magmodules.eu/help/' . self::MODULE_CODE;

    /**
     * @var GeneralHelper
     */
    private $generalHelper;

    /**
     * Version constructor.
     *
     * @param Context       $context
     * @param GeneralHelper $generalHelper
     */
    public function __construct(
        Context $context,
        GeneralHelper $generalHelper
    ) {
        $this->generalHelper = $generalHelper;
        parent::__construct($context);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function _getElementHtml(AbstractElement $element)
    {

        $html = '<a href="' . self::MANUAL_LINK . '" class="support-link">' . __('Online Manual') . '</a>';
        $html .= '&nbsp | &nbsp';
        $html .= '<a href="' . self::SUPPORT_LINK . '" class="support-link">' . __('FAQ') . '</a>';

        $element->setData('text', $html);
        return parent::_getElementHtml($element);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function _renderScopeLabel(AbstractElement $element)
    {
        return '';
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function _renderInheritCheckbox(AbstractElement $element)
    {
        return '';
    }
}
