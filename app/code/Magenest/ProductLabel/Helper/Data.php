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
namespace Magenest\ProductLabel\Helper;

use Magento\Catalog\Model\Product;

/**
 * Class Data
 *
 * @package Magenest\ProductLabel\Helper
 */
class Data
{
    /**
     * @param Product $product
     */
    public function __construct(
        Product $product
    ) {
        $this->product = $product;
    }

    /**
     * Check Product Label
     *
     * @param  \Magenest\ProductLabel\Model\Label $model
     * @param  int                                $id
     * @return bool
     */
    public function useLabel($model, $id)
    {
        $product = $this->product->load($id);
        $conditions = unserialize($model->getConditionsSerialized());
        if (!isset($conditions['conditions'])) {
            return $model->getId();
        }

        $value = [
            'aggregator' => $conditions['aggregator'],
            'value' => $conditions['value'],
            'conditions' => $conditions['conditions']
        ];

        $check = $this->prepareCombine($product, $value);

        if ($check) {
            return $model->getId();
        } else {
            return false;
        }
    }

    /**
     * @param Product $model
     * @param $value
     * @return bool
     */
    protected function prepareCondition(Product $model, $value)
    {
        $operator = $value['operator'];
        $attribute = $model->getData($value['attribute']);
        $result = $this->processData($attribute, $value['value']);

        $check = $this->processOperator($operator, $result);

        return $check;
    }

    /**
     * Combine
     *
     * @param Product $model
     * @param $value
     * @return bool
     */
    protected function prepareCombine(Product $model, $value)
    {
        $aggregator = $value['aggregator'];
        $ifCon = $value['value'];
        $conditions = $value['conditions'];

        $check = false;
        foreach ($conditions as $condition => $value) {
            $check = false;
            if (is_array($value) && $value['type'] == 'Magento\CatalogRule\Model\Rule\Condition\Product') {
                $check = $this->prepareCondition($model, $value);
            } elseif (is_array($value) && $value['type'] == 'Magento\CatalogRule\Model\Rule\Condition\Combine') {
                $check = $this->prepareCombine($model, $value);
            }

            if ($check && $aggregator == 'any') {
                break;
            } elseif (!$check && $aggregator != 'any') {
                break;
            }
        }

        if (!$ifCon) {
            if ($check) {
                $check = false;
            } else {
                $check = true;
            }
        }

        return $check;
    }

    /**
     * @param $operator
     * @param $result
     * @return bool
     */
    protected function processOperator($operator, $result)
    {
        $check = false;
        if ($operator == '==' || $operator == '()' || $operator == '{}') {
            if ($result) {
                $check = true;
            }
        } elseif ($operator == '!=' || $operator == '!()' || $operator == '!{}') {
            if (!$result) {
                $check = true;
            }
        }

        return $check;
    }

    /**
     * @param $attribute
     * @param $value
     * @return bool
     */
    protected function processData($attribute, $value)
    {
        if (is_array($value)) {
            $attribute = explode(',', $attribute);
        }
        if (is_array($attribute)) {
            $value = explode(',', str_replace(' ', '', $value));
        }
        if (is_array($value) && is_array($attribute)) {
            $result = array_intersect($attribute, $value);
            if (!empty($result)) {
                return true;
            }
        } else {
            if ($attribute == $value) {
                return true;
            }
        }

        return false;
    }
}
