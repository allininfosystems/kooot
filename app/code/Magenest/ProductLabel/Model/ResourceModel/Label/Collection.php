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
namespace Magenest\ProductLabel\Model\ResourceModel\Label;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;

/**
 * Class Collection
 *
 * @package Magenest\ProductLabel\Model\ResourceModel\Label
 */
class Collection extends AbstractCollection
{
    /**
     * Index Field N
     *
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     *  Initialize resource collection
     *
     *  @return void
     */
    public function _construct()
    {
        $this->_init('Magenest\ProductLabel\Model\Label', 'Magenest\ProductLabel\Model\ResourceModel\Label');
    }

    /**
     * Add enable filter to collection
     *
     * @return $this
     */
    public function addActiveFilter()
    {
        return $this->addFieldToFilter('status', 1);
    }

    /**
     * Add store availability filter. Include availability product
     * for store website
     *
     * @param null|string|bool|int $store
     * @return $this
     */
    public function addStoreToFilter($store = null)
    {
        $conditions = [
            'label_store.rule_id = main_table.id',
            'label_store.store_id = '.$store
        ];
        $this->getSelect()->join(
            ['label_store' => $this->getTable('magenest_productlabel_store')],
            join(' AND ', $conditions),
            []
        );

        return $this;
    }

    /**
     * Add Customers Groups to filter
     *
     * @param null $groups
     * @return $this
     */
    public function addCustomerGroupsToFilter($groups = null)
    {
        $conditions = [
            'label_customer_groups.rule_id = main_table.id',
            'label_customer_groups.customer_group_id = '.$groups
        ];
        $this->getSelect()->join(
            ['label_customer_groups' => $this->getTable('magenest_productlabel_customer_group')],
            join(' AND ', $conditions),
            []
        );

        return $this;
    }
}
