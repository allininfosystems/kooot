<?php

namespace Medma\MarketPlace\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\ModuleListInterface;

class Version extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MODULE_NAME = 'Medma_MarketPlace';

    protected $_moduleList;

    public function __construct(
        Context $context,
        ModuleListInterface $moduleList
    ) {
    
        $this->_moduleList = $moduleList;
        parent::__construct($context);
    }

    public function getVersion()
    {
        return $this->_moduleList
            ->getOne(self::MODULE_NAME)['setup_version'];
    }
}
