<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Medma\MarketPlace\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Product list
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'Magento\Catalog\Block\Product\ProductList\Toolbar';

    /**
     * Product Collection
     *
     * @var AbstractCollection
     */
    protected $_productCollection;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $_postDataHelper;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;


    /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    protected function _getProductCollection()
    {	
        if ($this->_productCollection === null) {
            $layer = $this->getLayer();
            /* @var $layer \Magento\Catalog\Model\Layer */
            if ($this->getShowRootCategory()) {
                $this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if ($this->_coreRegistry->registry('product')) {
                // get collection of categories this product is associated with
                $categories = $this->_coreRegistry->registry('product')
                    ->getCategoryCollection()->setPage(1, 1)
                    ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                try {
                    $category = $this->categoryRepository->get($this->getCategoryId());
                } catch (NoSuchEntityException $e) {
                    $category = null;
                }

                if ($category) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                }
            }
            $this->_productCollection = $layer->getProductCollection();
            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }
        }
       
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $status = $objectManager->create('Medma\MarketPlace\Helper\Data')->getProductConfig();
        if ($status == 1) {
            $collection = clone $this->_productCollection;
            $new_Collection = clone $this->_productCollection;

            $this->_productCollection->clear();
            $groupedcollection = $collection->groupByAttribute('name_keyword');
            $name_keyword=[];
            foreach ($groupedcollection as $gruop) {
                $name_keyword[] = $gruop->getNameKeyword();
            }
	    
            $names = array_unique($name_keyword);
            $products =[];
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            foreach ($names as $p => $q) {
                $products = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection')
                                            ->addAttributeToSelect('name_keyword')
                                            ->addAttributeToFilter('name_keyword', $q)
                                            ->setOrder('price', 'asc')->getFirstItem()
                                            ->getId();
            }
            if ($products) {
                return $this->_productCollection->addAttributeToFilter('entity_id', $products);
            } else {
                return $this->_productCollection;
            }
        } else { 
            return $this->_productCollection;
        }
    }
}
