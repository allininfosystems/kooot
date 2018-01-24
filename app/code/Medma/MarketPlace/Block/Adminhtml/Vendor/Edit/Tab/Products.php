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

class Products extends \Magento\Backend\Block\Widget\Grid\Extended
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
    protected $adminSession;
    protected $vendorHelper;
    protected $_status;

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
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Medma\MarketPlace\Helper\Data $vendorHelper,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_status = $status;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->adminSession = $adminSession;
        $this->vendorHelper = $vendorHelper;
        $this->registry = $registry;
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
        $roleId = $this->vendorHelper->getConfig('general', 'vendor_role');
        $current_user = $this->adminSession->getUser();
        if ($current_user->getRole()->getRoleId() == $roleId) {
            $vendor_id = $current_user->getId();
        } else {
            $vendor_id = $this->getRequest()->getParam('id');
        }

        $collection = $this->productCollectionFactory->create()
                    ->addFieldToFilter('vendor', $vendor_id)
                    ->addFieldToFilter('approved', 1);
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
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'index' => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'qty',
            [
                'header' => __('Qty'),
                'index' => 'qty',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'class' => 'xxx',
                'width' => '50px',
                'type'  => 'options',
                 'options'=> $this->_status->getOptionArray()
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
        return $this->getUrl('*/*/approvedproductgrid', ['_current'=>true]);
    }
}
