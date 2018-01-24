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
 
namespace Medma\MarketPlace\Block\Adminhtml\Feedback;
 
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
    protected $adminSession;
    
    /**
     * @var \Magento\Authorization\Model\RoleFactory $rolesFactory
     */
    protected $rolesFactory;
    protected $productFactory;
    protected $orderFactory;
    protected $feedbackFactory;
    protected $customerFactory;
    
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
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Medma\MarketPlace\Model\ResourceModel\Feedback\CollectionFactory $feedbackFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    ) {
        $this->profileFactory = $profileFactory;
        $this->UserFactory = $UserFactory;
        $this->rolesFactory = $rolesFactory;
        $this->productFactory = $productFactory;
        $this->orderFactory = $orderFactory;
        $this->vendorHelper = $vendorHelper;
        $this->adminSession = $adminSession;
        $this->feedbackFactory = $feedbackFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->customerFactory = $customerFactory;
        parent::__construct($context, $backendHelper, $data);
    }
 
    protected function _construct()
    {
        parent::_construct();
        $this->setId('orderGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
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
        $current_user = $this->adminSession->getUser();
        $profileFactory = $this->profileFactory->create()->getCollection()->addFieldToFilter('user_id', $current_user->getId())->getFirstItem();
        $collection = $this->feedbackFactory->create()->addFieldToFilter('vendor_id', $profileFactory->getId());
     
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
     
    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
            'header' => __('Id'),
            'align' => 'right',
            'width' => '80px',
            'index' => 'entity_id',
            ]
        );
        $this->addColumn(
            'customer_id',
            [
            'header' => __('Customer Name'),
            'align' => 'right',
            'width' => '80px',
            'index' => 'customer_id',
            'renderer' => 'Medma\MarketPlace\Block\Adminhtml\Feedback\Renderer\Customer',
            'filter' => false
            ]
        );
         $this->addColumn(
             'nickname',
             [
             'header' => __('Nickname'),
             'align' => 'right',
             'width' => '80px',
             'index' => 'nickname',
             ]
         );
          $this->addColumn(
              'summary',
              [
              'header' => __('Summary'),
              'align' => 'right',
              'width' => '80px',
              'index' => 'summary',
              ]
          );
          $this->addColumn(
              'review',
              [
              'header' => __('Reviews'),
              'align' => 'right',
              'width' => '80px',
              'index' => 'review',
              ]
          );
          $this->addColumn(
              'quality',
              [
              'header' => __('Quality Rating'),
              'align' => 'right',
              'width' => '80px',
              'index' => 'quality',
              ]
          );
           $this->addColumn(
               'price',
               [
               'header' => __('Price Rating'),
               'align' => 'right',
               'width' => '80px',
               'index' => 'price',
               ]
           );
           $this->addColumn(
               'shipping',
               [
               'header' => __('Shipping Time & Charges Rating'),
               'align' => 'right',
               'width' => '80px',
               'index' => 'shipping',
               ]
           );

           $this->addColumn(
               'created_at',
               [    'header' => __('Created At'),
               'index' => 'created_at',
               'type' => 'datetime',
               'width' => '100px',
               ]
           );

      
           return parent::_prepareColumns();
    }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/feedbackgrid', ['_current'=>true]);
    }
}
