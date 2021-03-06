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
 * Class Header
 *
 * @package Magmodules\TradeTracker\Block\Adminhtml\Magmodules
 */
class Header extends Field
{

    const MODULE_CODE = 'tradetracker-magento2';
    const MODULE_SUPPORT_LINK = 'https://www.magmodules.eu/help/' . self::MODULE_CODE;
    const MODULE_CONTACT_LINK = 'https://www.magmodules.eu/support.html?ext=' . self::MODULE_CODE;
    /**
     * @var string
     */
    protected $_template = 'Magmodules_TradeTracker::system/config/fieldset/header.phtml';
    /**
     * @var GeneralHelper
     */
    private $generalHelper;

    /**
     * Header constructor.
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
    public function render(AbstractElement $element)
    {
        $element->addClass('magmodules');

        return $this->toHtml();
    }

    /**
     * Image with extension and magento version.
     *
     * @return string
     */
    public function getImage()
    {
        $extVersion = $this->generalHelper->getExtensionVersion();
        $magVersion = $this->generalHelper->getMagentoVersion();

        return sprintf('https://www.magmodules.eu/logo/%s/%s/%s/logo.png', self::MODULE_CODE, $extVersion, $magVersion);
    }

    /**
     * Contact link for extension.
     *
     * @return string
     */
    public function getContactLink()
    {
        return self::MODULE_CONTACT_LINK;
    }

    /**
     * Support link for extension.
     *
     * @return string
     */
    public function getSupportLink()
    {
        return self::MODULE_SUPPORT_LINK;
    }
}
