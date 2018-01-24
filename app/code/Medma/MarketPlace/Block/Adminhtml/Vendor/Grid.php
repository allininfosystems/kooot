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
 
namespace Medma\MarketPlace\Block\Adminhtml\Vendor;
 
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
 
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;
 
    /**
     * @var \Medma\MarketPlace\Model\ProfileFactory $profileFactory
     */
    protected $profileFactory;
    
    /**
     * @var \Magento\User\Model\UserFactory $UserFactory
     */
    
    protected $UserFactory;
 
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    
    /**
     * @var \Medma\MarketPlace\Helper\Data $vendorHelper
     */
    protected $vendorHelper;
    
    /**
     * @var \Magento\Authorization\Model\RoleFactory $rolesFactory
     */
    protected $rolesFactory;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Medma\MarketPlace\Model\ProfileFactory $profileFactory
     * @param \Magento\User\Model\UserFactory $UserFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Medma\MarketPlace\Helper\Data $vendorHelper
     * @param \Magento\Authorization\Model\RoleFactory $rolesFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Medma\MarketPlace\Model\ProfileFactory $profileFactory,
        \Magento\User\Model\UserFactory $UserFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Medma\MarketPlace\Helper\Data $vendorHelper,
        \Magento\Authorization\Model\RoleFactory $rolesFactory,
        array $data = []
    ) {
        $this->profileFactory = $profileFactory;
        $this->UserFactory = $UserFactory;
        $this->rolesFactory = $rolesFactory;
        $this->vendorHelper = $vendorHelper;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }
 
    protected function _construct()
    {
        parent::_construct();
        $this->setId('VendorGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    
    /**
     * @method : this method prepare collection for the grid
     * @return : array
     */
    protected function _prepareCollection()
    {

        $roleId = $this->vendorHelper->getConfig('general', 'vendor_role');
        $roleUsers = $this->rolesFactory->create()->load($roleId)->getRoleUsers();
        $collection = $this->UserFactory->create()->getCollection()->addFieldToFilter('user_id', ['in' => $roleUsers]);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
     
    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'user_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'user_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'width' => '30px',
                ]
        );
                $this->addColumn(
                    'username',
                    [
                    'header' => __('User Name'),
                    'index' => 'username',
                    'class' => 'xxx',
                    'width' => '50px',
                    ]
                );
                $this->addColumn(
                    'firstname',
                    [
                    'header' => __('First Name'),
                    'index' => 'firstname',
                    'class' => 'xxx',
                    'width' => '50px',
                    ]
                );
                $this->addColumn(
                    'lastname',
                    [
                    'header' => __('Last Name'),
                    'index' => 'lastname',
                    'class' => 'xxx',
                    'width' => '50px',
                    ]
                );
                $this->addColumn(
                    'email',
                    [
                    'header' => __('Email'),
                    'class' => 'xxx',
                    'width' => '100px',
                    'index' => 'email'
                    ]
                );
                $this->addColumn(
                    'created_at',
                    [
                    'header' => __('Vendor Since'),
                    'type' => 'datetime',
                    'index' => 'created',
                    'class' => 'xxx',
                    'width' => '50px',
                    ]
                );
                
                //$status = $statuses = ['1' => __('&#10004;'), '0' => __('&#x2718;')];
                $this->addColumn(
                    'is_active',
                    [
                    'header' => __('Status'),
                    'index' => 'is_active',
                    'type' => 'options',
                    'renderer' => 'Medma\MarketPlace\Block\Adminhtml\Vendor\Renderer\Status',
                    //'options' => $status,
                    ]
                );
                $vacation_mode = $vacation_modes = ['1' => __('Enable'), '0' => __('Disable')];
                $this->addColumn(
                    'seller_vacation_mode',
                    [
                    'header' => __('Vacation Mode'),
                    'index' => 'seller_vacation_mode',
                    'type' => 'options',
                    'options' => $vacation_mode,
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
                    'caption' => __('&#x270D;'),
                    'url' => ['base' => '*/*/edit'],
                    'field' => 'id',
                    ],
                    ],
                    'filter' => false,
                    'sortable' => false,
                    'index' => 'stores',
                    'header_css_class' => 'col-action',
                    'column_css_class' => 'col-action',
                    ]
                );
                $this->addColumn(
                    'products',
                    [
                    'header' => __('Products'),
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => [
                    [
                    'caption' => __('Products'),
                    'url' => ['base' => 'admin_marketplace/product/index'],
                    'field' => 'id',
                    ],
                    ],
                    'filter' => false,
                    'sortable' => false,
                    'index' => 'stores',
                    'header_css_class' => 'col-action',
                    'column_css_class' => 'col-action',
                    ]
                );
                $this->addColumn(
                    'transaction',
                    [
                    'header' => __('Transaction Reports'),
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => [
                    [
                    'class' => 'fa fa-file',
                    'url' => ['base' => 'admin_marketplace/transaction/index'],
                    'field' => 'id',
                    ],
                    ],
                    'filter' => false,
                    'sortable' => false,
                    'index' => 'stores',
                    'header_css_class' => 'col-action',
                    'column_css_class' => 'col-action',
                    ]
                );
                $this->addColumn(
                    'config',
                    [
                    'header' => __('Config'),
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => [
                    [
                    'caption' => __('Config'),
                    'url' => ['base' => 'admin_marketplace/configuration/vendor'],
                    'field' => 'id',
                    ],
                    ],
                    'filter' => false,
                    'sortable' => false,
                    'index' => 'stores',
                    'header_css_class' => 'col-action',
                    'column_css_class' => 'col-action',
                    ]
                );
     
        return parent::_prepareColumns();
    }
     
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('user_id');
        $this->getMassactionBlock()->addItem(
            'Inactive',
            [
            'label' => __('Inactive'),
            'url' => $this->getUrl('admin_marketplace/*/massinactive'),
            'confirm' => __('Are you sure?'),
            ]
        );
        $this->getMassactionBlock()->addItem(
            'Active',
            [
            'label' => __('Active'),
            'url' => $this->getUrl('admin_marketplace/*/massactive'),
            'confirm' => __('Are you sure?'),
            ]
        );
        
        return $this;
    }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/vendorgrid', ['_current'=>true]);
    }
}
