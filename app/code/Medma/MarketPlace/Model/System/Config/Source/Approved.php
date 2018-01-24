<?php

/**
 * Medma Marketplace
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Team
 * that is bundled with this package of Medma Infomatix
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * Contact us Support does not guarantee correct work of this package
 * on any other Magento edition except Magento COMMUNITY edition.
 * =================================================================
 *
 * @category    Medma
 * @package     Medma_MarketPlace
 **/
namespace Medma\Marketplace\Model\System\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

class Approved extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function getAllOptions()
    {
        return [
                ['value' => 0, 'label' => __('No')],
                ['value' => 1, 'label' => __('Yes')],
                ['value' => 2, 'label' => __('Rejected')]
               ];
    }
}
