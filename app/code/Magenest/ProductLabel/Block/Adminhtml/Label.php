<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ProductLabel
 * @author   ThaoPV <thaopw@gmail.com>
 */
namespace Magenest\ProductLabel\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Label
 *
 * @package Magenest\ProductLabel\Block\Adminhtml
 */
class Label extends Container
{
    /**
     * Init
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magenest_ProductLabel';
        $this->_controller = 'adminhtml_label';
        $this->_addButtonLabel = __('Add New Label');
        parent::_construct();
    }
}
