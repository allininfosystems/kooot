<?php
/**
 *
 * Copyright © 2016 Medma. All rights reserved.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 */
 
namespace Medma\MarketPlace\Block\Adminhtml\Vendor\Edit\Tab;

class PendingProducts extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;
    
    /**
     * @var  \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryCollectionFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    protected $status;
    protected $approved;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Medma\MarketPlace\Model\Product\Attribute\Source\Approved $approved,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->registry = $registry;
        $this->_status = $status;
        $this->approved = $approved;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return : void
     */
    protected function _construct()
    {
  
        parent::_construct();
        $this->setId('catalog_category_products');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }
    
    /**
     * @method : this method retreive collection for grid
     * @return : Grid collection
     */
    protected function _prepareCollection()
    {

        $collection = $this->productCollectionFactory->create()
                            ->addFieldToFilter('approved', 0);
        $collection->addAttributeToSelect('vendor');
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('qty');
        $collection->addAttributeToSelect('price');
        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $collection->joinAttribute('approved', 'catalog_product/approved', 'entity_id', null, 'inner');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * @method : this method prepare columns for grid
     * @return : Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_product',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'align' => 'center',
                'index' => 'entity_id',
            ]
        );

        $this->addColumn(
            'entity_id',
            [
                'header' => __('Product ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        
        $this->addColumn(
            'vendor',
            [
                'header' => __('Vendor Name'),
                'index' => 'vendor',
                'class' => 'xxx',
                'width' => '50px',
                'renderer' => 'Medma\MarketPlace\Block\Adminhtml\Product\Renderer\Vendor'
            ]
        );

        $this->addColumn(
            'type_id',
            [
                'header' => __('Type'),
                'index' => 'type_id',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('Sku'),
                'index' => 'sku',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $store = $this->_getStore();
      // $store = $this->_getStore();
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
                'header_css_class' => 'col-price',
                'column_css_class' => 'col-price'
            ]
        );

        $this->addColumn(
            'edit',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => 'catalog/product/edit',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field' => 'id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @method : this method return label of tab
     * @return : string
     */
    public function getTabLabel()
    {
        return __('Select Product');
    }

    /**
     * @method : this method return title of tab
     * @return : string
     */
    public function getTabTitle()
    {
        return __('Select Product');
    }

    /**
     * @method : this method return true or false to show the tab
     * @return : boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @method : this method return true or false to hide the tab
     * @return : boolean
     */
    public function isHidden()
    {
        return false;
    }
    
    public function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store');
        return $this->_storeManager->getStore($storeId);
    }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/pendingproductgrid', ['_current'=>true]);
    }
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'catalog/*/edit',
            ['store' => $this->getRequest()->getParam('store'), 'id' => $row->getId()]
        );
    }
}
