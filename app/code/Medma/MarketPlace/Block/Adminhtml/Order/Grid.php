<?php
namespace Medma\MarketPlace\Block\Adminhtml\Order;
 
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
    protected $addressFactory;
    
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
        \Magento\Sales\Model\ResourceModel\Order\Grid\CollectionFactory $addressFactory,
        array $data = []
    ) {
        $this->profileFactory = $profileFactory;
        $this->UserFactory = $UserFactory;
        $this->rolesFactory = $rolesFactory;
        $this->productFactory = $productFactory;
        $this->orderFactory = $orderFactory;
        $this->vendorHelper = $vendorHelper;
        $this->adminSession = $adminSession;
        $this->addressFactory = $addressFactory;
        $this->_coreRegistry = $coreRegistry;
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
        $orderIds= [];
        if ($current_user->getRole()->getRoleId() == $roleId) {
            $productIds = $this->productFactory->create()->getCollection()
                          ->addAttributeToFilter('status', 1)
                          ->addAttributeToFilter('approved', 1)
                          ->addAttributeToFilter('vendor', $current_user->getId())->getAllIds();
        
                            
            $collection = $this->orderFactory->create()->getCollection();
            foreach ($collection as $order) {
                foreach ($order->getAllItems() as $item) {
                    $productId = $item->getData('product_id');
                    if (in_array($productId, $productIds)) {
                        $orderIds[] = $order->getId();
                     //   break;
                    }
                }
            }

            $new_collection = $this->addressFactory->create()->addFieldToFilter('entity_id', ['in' => $orderIds]);
        }

        $this->setCollection($new_collection);
        return parent::_prepareCollection();
    }
     
    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'increment_id',
            [
            'header' => __('Order #'),
            'align' => 'right',
            'width' => '80px',
            'index' => 'increment_id',
            ]
        );

        $this->addColumn(
            'created_at',
            [    'header' => __('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
            ]
        );

        $this->addColumn(
            'billing_name',
            [
            'header' => __('Bill to Name'),
            'index' => 'billing_address',
            'width' => '400px',
            ]
        );

        $this->addColumn(
            'shipping_name',
            [
            'header' => __('Ship to Name'),
            'index' => 'shipping_address',
            'width' => '400px',
            ]
        );

        $this->addColumn(
            'status',
            [
            'header' => __('Status'),
            'index' => 'status',
            //  'type' => 'options',
            'width' => '70px',
            // 'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
            ]
        );

        $this->addColumn(
            'action',
            [
            'header' => __('Action'),
            'width' => '50px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => [
                [
                    'caption' => __('View'),
                    'url' => ['base' => '*/*/view'],
                    'field' => 'order_id'
                ]
            ],
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
            ]
        );
          return parent::_prepareColumns();
    }
     
    /**
     * @return $this
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', ['order_id' => $row->getId()]);
    }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/ordergrid', ['_current'=>true]);
    }
}
