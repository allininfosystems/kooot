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
namespace Magenest\ProductLabel\Model;

use Magento\CatalogRule\Model\Rule;

/**
 * Class Label
 *
 * @package Magenest\ProductLabel\Model
 *
 * @method string getCategoryPosition()
 * @method string getCategoryImageUrl()
 * @method int getCategoryDisplay()
 * @method string getCategoryImage()
 * @method string getProductImage()
 * @method Label setCategoryImageUrl(string $url);
 * @method Label setProductImageUrl(string $url);
 * @method Label setStatus(int $status)
 */
class Label extends Rule
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'product_labels';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getRule() in this case
     *
     * @var string
     */
    protected $_eventObject = 'labels';

    /**
     * Init resource model and id field
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Magenest\ProductLabel\Model\ResourceModel\Label');
        $this->setIdFieldName('id');
    }
}
