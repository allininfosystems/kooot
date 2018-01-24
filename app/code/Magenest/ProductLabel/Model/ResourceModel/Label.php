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
namespace   Magenest\ProductLabel\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Label
 *
 * @package Magenest\ProductLabel\Model\ResourceModel
 */
class Label extends AbstractDb
{
    /**
     *  Initialize resource model
     */
    public function _construct()
    {
        $this->_init('magenest_productlabel_rule', 'id');
    }

    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_ids', $stores);
            $groups = $this->lookupCustomerGroupsIds($object->getId());
            $object->setData('customer_groups_ids', $groups);
        }

        return parent::_afterLoad($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $rule
     * @return $this
     */
    protected function _afterDelete(AbstractModel $rule)
    {
        $connection = $this->getConnection();
        $connection->delete(
            $this->getTable('magenest_productlabel_store'),
            ['rule_id=?' => $rule->getId()]
        );
        $connection->delete(
            $this->getTable('magenest_productlabel_customer_group'),
            ['rule_id=?' => $rule->getId()]
        );

        return parent::_afterDelete($rule);
    }

    /**
     * Assign rule to store views
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        $oldIds = $this->lookupStoreIds($object->getId());
        $newIds = (array)$object->getStoreIds();
        if (empty($newIds)) {
            $newIds = (array)$object->getStoreId();
        }
        $this->_updateForeignKey($object, $newIds, $oldIds, 'magenest_productlabel_store', 'store_id');

        $newIds = (array)$object->getCustomerGroupsIds();

        if (is_array($newIds)) {
            $oldIds = $this->lookupCustomerGroupsIds($object->getId());
            $this->_updateForeignKey($object, $newIds, $oldIds, 'magenest_productlabel_customer_group', 'customer_group_id');
        }

        return parent::_afterSave($object);
    }

    /**
     * Update post connections
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @param  Array $newRelatedIds
     * @param  Array $oldRelatedIds
     * @param  String $tableName
     * @param  String  $field
     * @return void
     */
    protected function _updateForeignKey(
        AbstractModel $object,
        array $newRelatedIds,
        array $oldRelatedIds,
        $tableName,
        $field
    ) {
        $table = $this->getTable($tableName);
        $insert = array_diff($newRelatedIds, $oldRelatedIds);
        $delete = array_diff($oldRelatedIds, $newRelatedIds);

        if ($delete) {
            $where = ['rule_id = ?' => (int)$object->getId(), $field.' IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = ['rule_id' => (int)$object->getId(), $field => (int)$storeId];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }
    }

    /**
     * Get Website ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        return $this->_lookupIds($id, 'magenest_productlabel_store', 'store_id');
    }

    /**
     * @param $id
     * @return array
     */
    protected function lookupCustomerGroupsIds($id)
    {
        return $this->_lookupIds($id, 'magenest_productlabel_customer_group', 'customer_group_id');
    }

    /**
     * Get ids to which specified item is assigned
     *
     * @param  int $id
     * @param  string $tableName
     * @param  string $field
     * @return array
     */
    protected function _lookupIds($id, $tableName, $field)
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from(
            $this->getTable($tableName),
            $field
        )->where(
            'rule_id = ?',
            (int)$id
        );

        return $adapter->fetchCol($select);
    }
}
